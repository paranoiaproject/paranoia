<?php
namespace Paranoia\Payment\Adapter;

use Paranoia\Common\Serializer\Serializer;
use Paranoia\Configuration\AbstractConfiguration;
use Paranoia\Formatter\FormatterInterface;
use Paranoia\Formatter\Gvp\ExpireDate;
use Paranoia\Formatter\IsoNumericCurrencyCode;
use Paranoia\Formatter\Money;
use Paranoia\Formatter\NopeFormatter;
use Paranoia\Formatter\SingleDigitInstallment;
use Paranoia\Payment\PaymentEventArg;
use Paranoia\Payment\Request;
use Paranoia\Payment\Response\PaymentResponse;
use Paranoia\Exception\BadResponseException;
use Paranoia\Exception\NotImplementedError;

class Gvp extends AdapterAbstract
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
        self::TRANSACTION_TYPE_PREAUTHORIZATION  => 'preauth',
        self::TRANSACTION_TYPE_POSTAUTHORIZATION => 'postauth',
        self::TRANSACTION_TYPE_SALE              => 'sales',
        self::TRANSACTION_TYPE_CANCEL            => 'void',
        self::TRANSACTION_TYPE_REFUND            => 'refund',
        self::TRANSACTION_TYPE_POINT_QUERY       => 'pointinquiry',
        self::TRANSACTION_TYPE_POINT_USAGE       => 'pointusage',
    );

    public function __construct(AbstractConfiguration $configuration)
    {
        parent::__construct($configuration);
        $this->currencyFormatter = new IsoNumericCurrencyCode();
        $this->amountFormatter = new Money();
        $this->installmentFormatter = new SingleDigitInstallment();
        $this->expireDateFormatter = new ExpireDate();
        $this->orderIdFormatter = new NopeFormatter();
    }

    /**
     * builds request base with common arguments.
     *
     * @param Request $request
     * @param string $transactionType
     *
     * @return array
     */
    private function buildBaseRequest(Request $request, $transactionType)
    {
        $terminal    = $this->buildTerminal($request, $transactionType);
        $customer    = $this->buildCustomer();
        $order       = $this->buildOrder($request);
        $transaction = $this->buildTransaction($request, $transactionType);
        return array(
            'Version'     => '0.01',
            'Mode'        => $this->configuration->getMode(),
            'Terminal'    => $terminal,
            'Order'       => $order,
            'Customer'    => $customer,
            'Transaction' => $transaction
        );
    }

    /**
     * builds terminal section of request.
     *
     * @param Request $request
     * * @param string $transactionType
     *
     * @return array
     */
    private function buildTerminal(Request $request, $transactionType)
    {
        list($username, $password) = $this->getApiCredentialsByRequest($transactionType);
        $hash = $this->getTransactionHash($request, $password, $transactionType);
        return array(
            'ProvUserID' => $username,
            'HashData'   => $hash,
            'UserID'     => $username,
            'ID'         => $this->configuration->getTerminalId(),
            'MerchantID' => $this->configuration->getMerchantId()
        );
    }

    /**
     * builds customer section of request.
     *
     * @return array
     */
    private function buildCustomer()
    {
        /**
         * we don't want to share customer information
         * to bank.
         */
        return array(
            'IPAddress'    => '127.0.0.1',
            'EmailAddress' => 'dummy@dummy.net'
        );
    }

    /**
     * builds card section of request.
     *
     * @param Request $request
     *
     * @return array
     */
    private function buildCard(Request $request)
    {
        $expireMonth = $this->expireDateFormatter->format(
            $request->getExpireMonth(),
            $request->getExpireYear()
        );
        return array(
            'Number'     => $request->getCardNumber(),
            'ExpireDate' => $expireMonth,
            'CVV2'       => $request->getSecurityCode()
        );
    }

    /**
     * builds order section of request.
     *
     * @param Request $request
     *
     * @return array
     */
    private function buildOrder(Request $request)
    {
        return array(
            'OrderID'     => $this->orderIdFormatter->format($request->getOrderId()),
            'GroupID'     => null,
            'Description' => null
        );
    }

    /**
     * builds terminal section of request.
     *
     * @param Request $request
     * @param string $transactionType
     * @param integer $cardHolderPresentCode
     * @param string  $originalRetrefNum
     *
     * @return array
     */
    private function buildTransaction(
        Request $request,
        $transactionType,
        $cardHolderPresentCode = 0,
        $originalRetrefNum = null
    ) {
        $installment     = $this->installmentFormatter->format($request->getInstallment());
        $amount          = $this->isAmountRequired($transactionType) ?
            $this->amountFormatter->format($request->getAmount()) : '1';
        $currency        = ($request->getCurrency()) ? $this->currencyFormatter->format($request->getCurrency()) : null;
        $type            = $this->getProviderTransactionType($transactionType);
        return array(
            'Type'                  => $type,
            'InstallmentCnt'        => $installment,
            'Amount'                => $amount,
            'CurrencyCode'          => $currency,
            'CardholderPresentCode' => $cardHolderPresentCode,
            'MotoInd'               => 'N',
            'OriginalRetrefNum'     => $originalRetrefNum
        );
    }

    /**
     * returns boolean true, when amount field is required
     * for request transaction type.
     *
     * @param string $transactionType
     *
     * @return boolean
     */
    private function isAmountRequired($transactionType)
    {
        return in_array(
            $transactionType,
            array(
                self::TRANSACTION_TYPE_SALE,
                self::TRANSACTION_TYPE_PREAUTHORIZATION,
                self::TRANSACTION_TYPE_POSTAUTHORIZATION,
            )
        );
    }

    /**
     * returns boolean true, when card number field is required
     * for request transaction type.
     *
     * @param string $transactionType
     *
     * @return boolean
     */
    private function isCardNumberRequired($transactionType)
    {
        return in_array(
            $transactionType,
            array(
                self::TRANSACTION_TYPE_SALE,
                self::TRANSACTION_TYPE_PREAUTHORIZATION,
            )
        );
    }

    /**
     * returns api credentials by transaction type of request.
     *
     * @param string $transactionType
     *
     * @return array
     */
    private function getApiCredentialsByRequest($transactionType)
    {
        $isAuth = in_array(
            $transactionType,
            array(
                self::TRANSACTION_TYPE_SALE,
                self::TRANSACTION_TYPE_PREAUTHORIZATION,
                self::TRANSACTION_TYPE_POSTAUTHORIZATION,
            )
        );
        if ($isAuth) {
            return array(
                $this->configuration->getAuthorizationUsername(),
                $this->configuration->getAuthorizationPassword()
            );
        } else {
            return array(
                $this->configuration->getRefundUsername(),
                $this->configuration->getRefundPassword()
            );
        }
    }

    /**
     * returns security hash for using in transaction hash.
     *
     * @param string $password
     *
     * @return string
     */
    private function getSecurityHash($password)
    {
        $tidPrefix  = str_repeat('0', 9 - strlen($this->configuration->getTerminalId()));
        $terminalId = sprintf('%s%s', $tidPrefix, $this->configuration->getTerminalId());
        return strtoupper(SHA1(sprintf('%s%s', $password, $terminalId)));
    }

    /**
     * returns transaction hash for using in transaction request.
     *
     * @param Request $request
     * @param string  $password
     * @param string $transactionType
     *
     * @return string
     */
    private function getTransactionHash(Request $request, $password, $transactionType)
    {
        $orderId      = $this->orderIdFormatter->format($request->getOrderId());
        $terminalId   = $this->configuration->getTerminalId();
        $cardNumber   = $this->isCardNumberRequired($transactionType) ? $request->getCardNumber() : '';
        $amount       = $this->isAmountRequired($transactionType) ?
            $this->amountFormatter->format($request->getAmount()) : '1';
        $securityData = $this->getSecurityHash($password);
        return strtoupper(
            sha1(
                sprintf(
                    '%s%s%s%s%s',
                    $orderId,
                    $terminalId,
                    $cardNumber,
                    $amount,
                    $securityData
                )
            )
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
            $rawRequest,
            array( 'root_name' => 'GVPSRequest' )
        );
        return array( 'data' => $xml );
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildPreauthorizationRequest()
     */
    protected function buildPreAuthorizationRequest(Request $request)
    {
        $requestData = array( 'Card' => $this->buildCard($request) );
        return array_merge($requestData, $this->buildBaseRequest($request, self::TRANSACTION_TYPE_PREAUTHORIZATION));
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildPostAuthorizationRequest()
     */
    protected function buildPostAuthorizationRequest(Request $request)
    {
        $requestData = $this->buildBaseRequest($request, self::TRANSACTION_TYPE_POSTAUTHORIZATION);
        return $requestData;
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildSaleRequest()
     */
    protected function buildSaleRequest(Request $request)
    {
        $requestData = array( 'Card' => $this->buildCard($request) );
        return array_merge($requestData, $this->buildBaseRequest($request, self::TRANSACTION_TYPE_SALE));
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildRefundRequest()
     */
    protected function buildRefundRequest(Request $request)
    {
        return $this->buildBaseRequest($request, self::TRANSACTION_TYPE_REFUND);
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildCancelRequest()
     */
    protected function buildCancelRequest(Request $request)
    {
        $requestData                = $this->buildBaseRequest($request, self::TRANSACTION_TYPE_CANCEL);
        $transactionId              = ($request->getTransactionId()) ? $request->getTransactionId() : null;
        $transaction                = $this->buildTransaction($request, 0, $transactionId);
        $requestData['Transaction'] = $transaction;
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
        $response->setIsSuccess('00' == (string)$xml->Transaction->Response->Code);
        $response->setResponseCode((string)$xml->Transaction->ReasonCode);
        if (!$response->isSuccess()) {
            $errorMessages = array();
            if (property_exists($xml->Transaction->Response, 'ErrorMsg')) {
                $errorMessages[] = sprintf(
                    'Error Message: %s',
                    (string)$xml->Transaction->Response->ErrorMsg
                );
            }
            if (property_exists($xml->Transaction->Response, 'SysErrMsg')) {
                $errorMessages[] = sprintf(
                    'System Error Message: %s',
                    (string)$xml->Transaction->Response->SysErrMsg
                );
            }
            $errorMessage = implode(' ', $errorMessages);
            $response->setResponseMessage($errorMessage);
        } else {
            $response->setResponseMessage('Success');
            $response->setOrderId((string)$xml->Order->OrderID);
            $response->setTransactionId((string)$xml->Transaction->RetrefNum);
        }
        $event = $response->isSuccess() ? self::EVENT_ON_TRANSACTION_SUCCESSFUL : self::EVENT_ON_TRANSACTION_FAILED;
        $this->getDispatcher()->dispatch($event, new PaymentEventArg(null, $response, $transactionType));
        return $response;
    }
}
