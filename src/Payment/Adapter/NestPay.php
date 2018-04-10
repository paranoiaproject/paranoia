<?php
namespace Paranoia\Payment\Adapter;

use Paranoia\Common\Serializer\Serializer;
use Paranoia\Configuration\AbstractConfiguration;
use Paranoia\Formatter\Decimal;
use Paranoia\Formatter\FormatterInterface;
use Paranoia\Formatter\IsoNumericCurrencyCode;
use Paranoia\Formatter\NestPay\ExpireDate;
use Paranoia\Formatter\NopeFormatter;
use Paranoia\Formatter\SingleDigitInstallment;
use Paranoia\Payment\PaymentEventArg;
use Paranoia\Payment\Request;
use Paranoia\Payment\Response\PaymentResponse;
use Paranoia\Exception\BadResponseException;
use Paranoia\Exception\NotImplementedError;

class NestPay extends AdapterAbstract
{
    /**
     * @var FormatterInterface
     */
    private $currencyFormatter;

    /**
     * @var FormatterInterface
     */
    private $amountFormatter;

    /**
     * @var FormatterInterface
     */
    private $installmentFormatter;

    /**
     * @var FormatterInterface
     */
    private $expireDateFormatter;

    /**
     * @var FormatterInterface
     */
    private $orderIdFormatter;

    public function __construct(AbstractConfiguration $configuration)
    {
        parent::__construct($configuration);
        $this->currencyFormatter = new IsoNumericCurrencyCode();
        $this->amountFormatter = new Decimal();
        $this->installmentFormatter = new SingleDigitInstallment();
        $this->expireDateFormatter = new ExpireDate();
        $this->orderIdFormatter = new NopeFormatter();
    }

    /**
     * @var array
     */
    protected $transactionMap = array(
        self::TRANSACTION_TYPE_PREAUTHORIZATION  => 'PreAuth',
        self::TRANSACTION_TYPE_POSTAUTHORIZATION => 'PostAuth',
        self::TRANSACTION_TYPE_SALE              => 'Auth',
        self::TRANSACTION_TYPE_CANCEL            => 'Void',
        self::TRANSACTION_TYPE_REFUND            => 'Credit',
        self::TRANSACTION_TYPE_POINT_QUERY       => '',
        self::TRANSACTION_TYPE_POINT_USAGE       => '',
    );

    /**
     * builds request base with common arguments.
     *
     * @return array
     */
    private function buildBaseRequest()
    {
        return array(
            'Name'     => $this->configuration->getUsername(),
            'Password' => $this->configuration->getPassword(),
            'ClientId' => $this->configuration->getClientId(),
            'Mode'     => $this->configuration->getMode()
        );
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildRequest()
     */
    protected function buildRequest(Request $request, $requestBuilder)
    {
        $rawRequest = call_user_func(array( $this, $requestBuilder ), $request);
        $serializer = new Serializer(Serializer::XML);
        $xml        = $serializer->serialize(
            array_merge($rawRequest, $this->buildBaseRequest()),
            array( 'root_name' => 'CC5Request' )
        );
        return array( 'DATA' => $xml );
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildPreauthorizationRequest()
     */
    protected function buildPreAuthorizationRequest(Request $request)
    {
        $amount      = $this->amountFormatter->format($request->getAmount());
        $installment = $this->installmentFormatter->format($request->getInstallment());
        $currency    = $this->currencyFormatter->format($request->getCurrency());
        $expireMonth = $this->expireDateFormatter->format($request->getExpireMonth(), $request->getExpireYear());
        $type        = $this->getProviderTransactionType(self::TRANSACTION_TYPE_PREAUTHORIZATION);
        $requestData = array(
            'Type'     => $type,
            'Total'    => $amount,
            'Currency' => $currency,
            'Taksit'   => $installment,
            'Number'   => $request->getCardNumber(),
            'Cvv2Val'  => $request->getSecurityCode(),
            'Expires'  => $expireMonth,
            'OrderId'  => $this->orderIdFormatter->format($request->getOrderId()),
        );
        return $requestData;
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildPostAuthorizationRequest()
     */
    protected function buildPostAuthorizationRequest(Request $request)
    {
        $type        = $this->getProviderTransactionType(self::TRANSACTION_TYPE_POSTAUTHORIZATION);
        $requestData = array(
            'Type'    => $type,
            'OrderId' => $this->orderIdFormatter->format($request->getOrderId()),
        );
        return $requestData;
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildSaleRequest()
     */
    protected function buildSaleRequest(Request $request)
    {
        $amount      = $this->amountFormatter->format($request->getAmount());
        $installment = $this->installmentFormatter->format($request->getInstallment());
        $currency    = $this->currencyFormatter->format($request->getCurrency());
        $expireMonth = $this->expireDateFormatter->format($request->getExpireMonth(), $request->getExpireYear());
        $type        = $this->getProviderTransactionType(self::TRANSACTION_TYPE_SALE);
        $requestData = array(
            'Type'     => $type,
            'Total'    => $amount,
            'Currency' => $currency,
            'Taksit'   => $installment,
            'Number'   => $request->getCardNumber(),
            'Cvv2Val'  => $request->getSecurityCode(),
            'Expires'  => $expireMonth,
            'OrderId'  => $this->orderIdFormatter->format($request->getOrderId()),
        );
        return $requestData;
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildRefundRequest()
     */
    protected function buildRefundRequest(Request $request)
    {
        $amount      = $this->amountFormatter->format($request->getAmount());
        $currency    = $this->currencyFormatter->format($request->getCurrency());
        $type        = $this->getProviderTransactionType(self::TRANSACTION_TYPE_REFUND);
        $requestData = array(
            'Type'     => $type,
            'Total'    => $amount,
            'Currency' => $currency,
            'OrderId'  => $this->orderIdFormatter->format($request->getOrderId()),
        );
        return $requestData;
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildCancelRequest()
     */
    protected function buildCancelRequest(Request $request)
    {
        $type        = $this->getProviderTransactionType(self::TRANSACTION_TYPE_CANCEL);
        $requestData = array(
            'Type'    => $type,
            'OrderId' => $this->orderIdFormatter->format($request->getOrderId()),
        );
        if ($request->getTransactionId()) {
            $requestData['TransId'] = $request->getTransactionId();
        }
        return $requestData;
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::parseResponse()
     */
    protected function buildPointQueryRequest(Request $request)
    {
        throw new NotImplementedError();
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildPointUsageRequest()
     */
    protected function buildPointUsageRequest(Request $request)
    {
        throw new NotImplementedError();
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::parseResponse()
     */
    protected function parseResponse($rawResponse, $transactionType)
    {
        $response = new PaymentResponse();
        try {
            /**
             * @var object $xml
             */
            $xml = new \SimpleXmlElement($rawResponse);
        } catch (\Exception $e) {
            $exception = new BadResponseException('Provider returned unexpected response: ' . $rawResponse);
            $eventArg = new PaymentEventArg(null, null, $transactionType, $exception);
            $this->getDispatcher()->dispatch(self::EVENT_ON_EXCEPTION, $eventArg);
            throw $exception;
        }
        $response->setIsSuccess((string)$xml->Response == 'Approved');
        $response->setResponseCode((string)$xml->ProcReturnCode);
        if (!$response->isSuccess()) {
            $errorMessages = array();
            if (property_exists($xml, 'Error')) {
                $errorMessages[] = sprintf('Error: %s', (string)$xml->Error);
            }
            if (property_exists($xml, 'ErrMsg')) {
                $errorMessages[] = sprintf(
                    'Error Message: %s ',
                    (string)$xml->ErrMsg
                );
            }
            if (property_exists($xml, 'Extra') && property_exists($xml->Extra, 'HOSTMSG')) {
                $errorMessages[] = sprintf(
                    'Host Message: %s',
                    (string)$xml->Extra->HOSTMSG
                );
            }
            $errorMessage = implode(' ', $errorMessages);
            $response->setResponseMessage($errorMessage);
        } else {
            $response->setResponseMessage('Success');
            $response->setOrderId((string)$xml->OrderId);
            $response->setTransactionId((string)$xml->TransId);
        }
        $event = $response->isSuccess() ? self::EVENT_ON_TRANSACTION_SUCCESSFUL : self::EVENT_ON_TRANSACTION_FAILED;
        $this->getDispatcher()->dispatch($event, new PaymentEventArg(null, $response, $transactionType));
        return $response;
    }
}
