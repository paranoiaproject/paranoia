<?php
namespace Paranoia\Payment\Adapter;

use Paranoia\Common\Serializer\Serializer;
use Paranoia\Payment\PaymentEventArg;
use Paranoia\Payment\Request;
use Paranoia\Payment\ConfirmRequest;
use Paranoia\Payment\Response\PaymentResponse;
use Paranoia\Payment\Exception\UnexpectedResponse;
use Paranoia\Payment\Exception\UnimplementedMethod;
use Paranoia\Payment\Exception\ResponseVerificationError;

class Gvp extends AdapterAbstract
{
    /**
     * @var array
     */
    protected $transactionMap = array(
        self::TRANSACTION_TYPE_PREAUTHORIZATION  => 'preauth',
        self::TRANSACTION_TYPE_POSTAUTHORIZATION => 'postauth',
        self::TRANSACTION_TYPE_SALE              => 'sales',
        self::TRANSACTION_TYPE_SALE_3D           => 'sales',
        self::TRANSACTION_TYPE_CANCEL            => 'void',
        self::TRANSACTION_TYPE_REFUND            => 'refund',
        self::TRANSACTION_TYPE_POINT_QUERY       => 'pointinquiry',
        self::TRANSACTION_TYPE_POINT_USAGE       => 'pointusage',
    );

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
    private function buildCustomer($ipAddress = null, $email = null)
    {
        /**
         * we don't want to share customer information
         * to bank.
         */
        return array(
            'IPAddress'    => !is_null($ipAddress) ? $ipAddress : '127.0.0.1',
            'EmailAddress' => !is_null($email)     ? $email     : 'dummy@dummy.net'
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
        $expireMonth = $this->formatExpireDate(
            $request->getExpireMonth(),
            $request->getExpireYear()
        );
        return array(
            'Number'     => $this->formatCardNumber($request->getCardNumber()),
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
            'OrderID'     => $this->formatOrderId($request->getOrderId()),
            'GroupID'     => $request->getGroupId(),
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
        $installment     = ($request->getInstallment()) ? $this->formatInstallment($request->getInstallment()) : null;
        $amount          = $this->isAmountRequired($transactionType) ? $this->formatAmount($request->getAmount()) : '1';
        $currency        = ($request->getCurrency()) ? $this->formatCurrency($request->getCurrency()) : null;
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

    private function build3DTransaction(ConfirmRequest $confirmRequest, $transactionType)
    {
        $transactionData = $this->buildTransaction($confirmRequest->getRequest(), $transactionType);

        $payload = $confirmRequest->getPayload();
        $secure3D = array(
            'AuthenticationCode' => $payload['cavv'],
            'SecurityLevel'      => $payload['eci'],
            'TxnID'              => $payload['xid'],
            'Md'                 => $payload['md'],
        );
        $transactionData['Secure3D'] = $secure3D;

        return $transactionData;
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
                self::TRANSACTION_TYPE_SALE_3D,
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
                self::TRANSACTION_TYPE_SALE_3D,
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
                self::TRANSACTION_TYPE_SALE_3D,
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
        $orderId      = $this->formatOrderId($request->getOrderId());
        $terminalId   = $this->configuration->getTerminalId();
        $cardNumber   = $this->isCardNumberRequired($transactionType) ? $this->formatCardNumber($request->getCardNumber()) : '';
        $amount       = $this->isAmountRequired($transactionType) ? $this->formatAmount($request->getAmount()) : '1';
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

    private function getTransaction3DHash(Request $request, $password, $transactionType)
    {
        $orderId      = $this->formatOrderId($request->getOrderId());
        $terminalId   = $this->configuration->getTerminalId();
        $cardNumber   = $this->isCardNumberRequired($transactionType) ? $this->formatCardNumber($request->getCardNumber()) : '';
        $amount       = $this->isAmountRequired($transactionType) ? $this->formatAmount($request->getAmount()) : '1';
        $installment  = $this->formatInstallment($request->getInstallment());
        $securityData = $this->getSecurityHash($password);

        return strtoupper(
            sha1(
                sprintf(
                    '%s%s%s%s%s%s%s%s%s',
                    $terminalId,
                    $orderId,
                    $amount,
                    $this->configuration->getSuccessUrl(),
                    $this->configuration->getErrorUrl(),
                    $this->getProviderTransactionType(self::TRANSACTION_TYPE_SALE_3D),
                    $installment,
                    $this->configuration->getSecureKey(),
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

    protected function buildConfirmRequest(ConfirmRequest $confirmRequest, $requestBuilder)
    {
        $rawRequest = call_user_func(array( $this, $requestBuilder ), $confirmRequest);
        $serializer = new Serializer(Serializer::XML);
        $xml        = $serializer->serialize(
            $rawRequest,
            array( 'root_name' => 'GVPSRequest' )
        );
        return array( 'data' => $xml );
    }

    public function build3DRequest(Request $request, $requestBuilder)
    {
        return call_user_func(array( $this, $requestBuilder ), $request);
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

    protected function buildSale3DRequest(Request $request)
    {
        $cardNumber  = $this->formatCardNumber($request->getCardNumber());
        $installment = $this->formatInstallment($request->getInstallment());
        $amount      = $this->formatAmount($request->getAmount());
        $cardYear    = $this->formatExpireYear($request->getExpireYear());
        $cardMonth   = $this->formatExpireMonth($request->getExpireMonth());
        $currency    = $this->formatCurrency($request->getCurrency());

        $hashData = $this->getTransaction3DHash($request, $this->configuration->getAuthorizationPassword(), self::TRANSACTION_TYPE_SALE_3D);

        $requestData = array(
            'cardnumber'            => $cardNumber,
            'cardexpiredatemonth'   => $cardMonth,
            'cardexpiredateyear'    => $cardYear,
            'mode'                  => $this->configuration->getMode(),
            'orderid'               => $request->getOrderId(),
            'ordergroupid'          => $request->getOrderId(),
            'cardcvv2'              => $request->getSecurityCode(),
            'apiversion'            => 'v0.01',
            'terminalprovuserid'    => $this->configuration->getAuthorizationUsername(),
            'terminaluserid'        => $this->configuration->getAuthorizationUsername(),
            'terminalid'            => $this->configuration->getTerminalId(),
            'terminalmerchantid'    => $this->configuration->getMerchantId(),
            'customeripaddress'     => $request->getIPAddress(),
            'customeremailaddress'  => $request->getEmail(),
            'txntype'               => $this->getProviderTransactionType(self::TRANSACTION_TYPE_SALE_3D),
            'secure3dsecuritylevel' => '3D',
            'txnamount'             => $amount,
            'txncurrencycode'       => $currency,
            'companyname'           => $this->configuration->getAuthorizationUsername(),
            'txninstallmentcount'   => $installment,
            'successurl'            => $this->configuration->getSuccessUrl(),
            'errorurl'              => $this->configuration->getErrorUrl(),
            'secure3dhash'          => $hashData,
            'refreshtime'           => '60',
            'lang'                  => 'tr',
            'txnmotoind'            => 'N',
            'orderdescription'      => ''
        );

        return $requestData;
    }

    protected function buildSale3DConfirmRequest(ConfirmRequest $confirmRequest)
    {
        $request     = $confirmRequest->getRequest();
        $payload     = $confirmRequest->getPayload();
        $amount      = $this->formatAmount($request->getAmount());
        $installment = $this->formatInstallment($request->getInstallment());
        $currency    = $this->formatCurrency($request->getCurrency());
        $type        = $this->getProviderTransactionType(self::TRANSACTION_TYPE_SALE_3D);

        $requestData                = $this->buildBaseRequest($request, self::TRANSACTION_TYPE_SALE_3D);
        $requestData['Transaction'] = $this->build3DTransaction($confirmRequest, self::TRANSACTION_TYPE_SALE_3D);

        return $requestData;
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
        throw new UnimplementedMethod();
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildPointUsageRequest()
     */
    protected function buildPointUsageRequest(Request $request)
    {
        throw new UnimplementedMethod();
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
        } catch ( \Exception $e ) {
            $exception = new UnexpectedResponse('Provider returned unexpected response: ' . $rawResponse);
            $eventArg = new PaymentEventArg(null, null, $transactionType, $exception);
            $this->getDispatcher()->dispatch(self::EVENT_ON_EXCEPTION, $eventArg);
            throw $exception;
        }
        $response->setIsSuccess('00' == (string)$xml->Transaction->Response->Code);
        $response->setResponseCode((string)$xml->Transaction->ReasonCode);
        $response->setRawResponse($xml);
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
            $response->setAuthCode((string)$xml->Transaction->AuthCode);
        }
        $event = $response->isSuccess() ? self::EVENT_ON_TRANSACTION_SUCCESSFUL : self::EVENT_ON_TRANSACTION_FAILED;
        $this->getDispatcher()->dispatch($event, new PaymentEventArg(null, $response, $transactionType));
        return $response;
    }

    protected function check3DHashIntegrity($payload) {
        $params    = explode(':', $payload['hashparams']);
        $secureKey = $this->configuration->getSecureKey();
        $hash      = '';

        foreach($params as $param) {
            if (!empty($param))
                $hash .= is_null($payload[$param]) ? '' : $payload[$param];
        }

        if($this->hashBase64($hash . $secureKey) == $payload['hash'])
            return true;

        return false;
    }

    protected function parseBank3DResponse($rawResponse)
    {
        $response = new PaymentResponse();
        $response->setOrderId($rawResponse['orderid']);
        $response->setTransactionId($rawResponse['orderid']);
        $response->setMdStatus($rawResponse['mdstatus']);
        $response->setResponseMessage($rawResponse['mderrormessage']);
        return $response;
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::formatAmount()
     */
    protected function formatAmount($amount, $reverse = false)
    {
        return str_replace(".", "", number_format($amount, 2, '.', ''));
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::formatExpireDate()
     */
    protected function formatExpireDate($month, $year)
    {
        return sprintf('%02s%s', $month, substr($year, -2));
    }
}
