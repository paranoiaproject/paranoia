<?php
namespace Paranoia\Pos;

use Paranoia\Common\Serializer\Serializer;
use Paranoia\Configuration\AbstractConfiguration;
use Paranoia\Formatter\FormatterInterface;
use Paranoia\Formatter\Money;
use Paranoia\Formatter\MultiDigitInstallment;
use Paranoia\Formatter\Posnet\ExpireDate;
use Paranoia\Formatter\Posnet\OrderId;
use Paranoia\Formatter\PosnetCurrencyCode;
use Paranoia\Event\TransactionEvent;
use Paranoia\Request;
use Paranoia\Response\PaymentResponse;
use Paranoia\Exception\BadResponseException;
use Paranoia\Exception\NotImplementedError;

class Posnet extends AbstractPos
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

    /**
     * @var array
     */
    protected $transactionMap = array(
        self::TRANSACTION_TYPE_PREAUTHORIZATION  => 'auth',
        self::TRANSACTION_TYPE_POSTAUTHORIZATION => 'capt',
        self::TRANSACTION_TYPE_SALE              => 'sale',
        self::TRANSACTION_TYPE_CANCEL            => 'reverse',
        self::TRANSACTION_TYPE_REFUND            => 'return',
        self::TRANSACTION_TYPE_POINT_QUERY       => 'pointinquiry',
        self::TRANSACTION_TYPE_POINT_USAGE       => 'pointusage',
    );

    public function __construct(AbstractConfiguration $configuration)
    {
        parent::__construct($configuration);
        $this->currencyFormatter = new PosnetCurrencyCode();
        $this->amountFormatter = new Money();
        $this->installmentFormatter = new MultiDigitInstallment();
        $this->expireDateFormatter = new ExpireDate();
        $this->orderIdFormatter = new OrderId();
    }


    /**
     * builds request base with common arguments.
     *
     * @return array
     */
    private function buildBaseRequest()
    {
        return array(
            'mid'      => $this->configuration->getMerchantId(),
            'tid'      => $this->configuration->getTerminalId()
        );
    }

    /**
     * {@inheritdoc}
     * @see \Paranoia\Pos\AbstractPos::buildRequest()
     */
    protected function buildRequest(Request $request, $requestBuilder)
    {
        $rawRequest = call_user_func(array( $this, $requestBuilder ), $request);
        $serializer = new Serializer(Serializer::XML);
        $xml        = $serializer->serialize(
            array_merge($this->buildBaseRequest(), $rawRequest),
            array( 'root_name' => 'posnetRequest' )
        );
        return array( 'xmldata' => $xml );
    }

    /**
     * {@inheritdoc}
     * @see \Paranoia\Pos\AbstractPos::buildPreauthorizationRequest()
     */
    protected function buildPreauthorizationRequest(Request $request)
    {
        $amount      = $this->amountFormatter->format($request->getAmount());
        $installment = $this->installmentFormatter->format($request->getInstallment());
        $currency    = $this->currencyFormatter->format($request->getCurrency());
        $expireMonth = $this->expireDateFormatter->format($request->getExpireMonth(), $request->getExpireYear());
        $type        = $this->getProviderTransactionType(self::TRANSACTION_TYPE_PREAUTHORIZATION);
        $requestData = array(
            $type => array(
                'ccno'          => $request->getCardNumber(),
                'expDate'       => $expireMonth,
                'cvc'           => $request->getSecurityCode(),
                'amount'        => $amount,
                'currencyCode'  => $currency,
                'orderID'       => $this->orderIdFormatter->format($request->getOrderId()),
                'installment'   => $installment,
                #TODO: this fields will be used, when point and some bank benefit usage is implemented.
                // 'extraPoint'    => "000000",
                // 'multiplePoint' => "000000"
            )
        );
        return $requestData;
    }

    /**
     * {@inheritdoc}
     * @see \Paranoia\Pos\AbstractPos::buildPostAuthorizationRequest()
     */
    protected function buildPostAuthorizationRequest(Request $request)
    {
        $amount      = $this->amountFormatter->format($request->getAmount());
        $installment = $this->installmentFormatter->format($request->getInstallment());
        $currency    = $this->currencyFormatter->format($request->getCurrency());
        $type        = $this->getProviderTransactionType(self::TRANSACTION_TYPE_POSTAUTHORIZATION);
        $requestData = array(
            $type => array(
                'hostLogKey'    => $request->getTransactionId(),
                'authCode'      => $request->getAuthCode(),
                'amount'        => $amount,
                'currencyCode'  => $currency,
                'installment'   => $installment,
                #TODO: this fields will be used, when point and some bank benefit usage is implemented.
                // 'extraPoint'    => "000000",
                // 'multiplePoint' => "000000"
            )
        );
        return $requestData;
    }

    /**
     * {@inheritdoc}
     * @see \Paranoia\Pos\AbstractPos::buildSaleRequest()
     */
    protected function buildSaleRequest(Request $request)
    {
        $amount      = $this->amountFormatter->format($request->getAmount());
        $installment = $this->installmentFormatter->format($request->getInstallment());
        $currency    = $this->currencyFormatter->format($request->getCurrency());
        $expireMonth = $this->expireDateFormatter->format($request->getExpireMonth(), $request->getExpireYear());
        $type        = $this->getProviderTransactionType(self::TRANSACTION_TYPE_SALE);
        $requestData = array(
            $type => array(
                'ccno'          => $request->getCardNumber(),
                'expDate'       => $expireMonth,
                'cvc'           => $request->getSecurityCode(),
                'amount'        => $amount,
                'currencyCode'  => $currency,
                'orderID'       => $this->orderIdFormatter->format($request->getOrderId()),
                'installment'   => $installment,
                #TODO: this fields will be used, when point and some bank benefit usage is implemented.
                // 'extraPoint'    => "000000",
                // 'multiplePoint' => "000000"
            )
        );
        return $requestData;
    }

    /**
     * {@inheritdoc}
     * @see \Paranoia\Pos\AbstractPos::buildRefundRequest()
     */
    protected function buildRefundRequest(Request $request)
    {
        $amount      = $this->amountFormatter->format($request->getAmount());
        $currency    = $this->currencyFormatter->format($request->getCurrency());
        $type        = $this->getProviderTransactionType(self::TRANSACTION_TYPE_REFUND);
        $requestData = array(
            $type => array(
                'hostLogKey'   => $request->getTransactionId(),
                'amount'       => $amount,
                'currencyCode' => $currency
            )
        );
        return $requestData;
    }

    /**
     * {@inheritdoc}
     * @see \Paranoia\Pos\AbstractPos::buildCancelRequest()
     */
    protected function buildCancelRequest(Request $request)
    {
        $type        = $this->getProviderTransactionType(self::TRANSACTION_TYPE_CANCEL);
        $requestData = array(
            $type => array(
                'transaction' => "sale",
                'hostLogKey'  => $request->getTransactionId(),
                'authCode'    => $request->getAuthCode()
            )
        );
        return $requestData;
    }

    /**
     * {@inheritdoc}
     * @see \Paranoia\Pos\AbstractPos::parseResponse()
     */
    protected function buildPointQueryRequest(Request $request)
    {
        throw new NotImplementedError();
    }

    /**
     * {@inheritdoc}
     * @see \Paranoia\Pos\AbstractPos::buildPointUsageRequest()
     */
    protected function buildPointUsageRequest(Request $request)
    {
        throw new NotImplementedError();
    }

    /**
     * {@inheritdoc}
     * @see \Paranoia\Pos\AbstractPos::parseResponse()
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
            $eventArg = new TransactionEvent(null, null, $transactionType, $exception);
            $this->getDispatcher()->dispatch(self::EVENT_ON_EXCEPTION, $eventArg);
            throw $exception;
        }
        $response->setIsSuccess((int)$xml->approved > 0);
        if (!$response->isSuccess()) {
            $response->setResponseCode((string)$xml->respCode);
            $errorMessages = array();
            if (property_exists($xml, 'respCode')) {
                $errorMessages[] = sprintf('Error: %s', (string)$xml->respCode);
            }
            if (property_exists($xml, 'respText')) {
                $errorMessages[] = sprintf('Error Message: %s ', (string)$xml->respText);
            }
            $errorMessage = implode(' ', $errorMessages);
            $response->setResponseMessage($errorMessage);
        } else {
            $response->setResponseCode("0000");
            $response->setResponseMessage('Success');
            if (property_exists($xml, 'orderId')) {
                $response->setOrderId((string)$xml->orderId);
            }
            $response->setTransactionId((string)$xml->hostlogkey);
            if (property_exists($xml, 'authCode')) {
                $response->setOrderId((string)$xml->authCode);
            }
        }
        $event = $response->isSuccess() ? self::EVENT_ON_TRANSACTION_SUCCESSFUL : self::EVENT_ON_TRANSACTION_FAILED;
        $this->getDispatcher()->dispatch($event, new TransactionEvent(null, $response, $transactionType));
        return $response;
    }
}
