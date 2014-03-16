<?php
namespace Paranoia\Payment\Adapter;

use Paranoia\Common\Serializer\Serializer;
use Paranoia\Payment\Request;
use Paranoia\Payment\Response\PaymentResponse;
use Paranoia\Payment\Exception\UnexpectedResponse;
use Paranoia\Payment\Exception\UnimplementedMethod;
use Paranoia\Communication\Connector;

class Gvp extends AdapterAbstract implements AdapterInterface
{

    const CONNECTOR_TYPE = Connector::CONNECTOR_TYPE_HTTP;
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

    /**
     * builds request base with common arguments.
     *
     * @param Request $request
     * @return array
     */
    private function buildBaseRequest(Request $request)
    {
        $terminal    = $this->buildTerminal($request);
        $customer    = $this->buildCustomer();
        $order       = $this->buildOrder($request);
        $transaction = $this->buildTransaction($request);
        return array(
            'Version'     => '0.01',
            'Mode'        => $this->config->mode,
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
     * @return array
     */
    private function buildTerminal(Request $request)
    {
        $config = $this->config;
        list($username, $password) = $this->getApiCredentialsByRequest(
            $request->getTransactionType()
        );
        $hash = $this->getTransactionHash($request, $password);
        return array(
            'ProvUserID' => $username,
            'HashData'   => $hash,
            'UserID'     => $username,
            'ID'         => $config->terminal_id,
            'MerchantID' => $config->merchant_id
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
     * @return array
     */
    private function buildCard(Request $request)
    {
        $expireMonth = $this->formatExpireDate(
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
     * @return array
     */
    private function buildOrder(Request $request)
    {
        return array(
            'OrderID'     => $request->getOrderId(),
            'GroupID'     => null,
            'Description' => null
        );
    }

    /**
     * builds terminal section of request.
     *
     * @param Request $request
     * @param integer $cardHolderPresentCode
     * @param string  $originalRetrefNum
     * @return array
     */
    private function buildTransaction(
        Request $request,
        $cardHolderPresentCode = 0,
        $originalRetrefNum = null
    ) {
        $transactionType = $request->getTransactionType();
        $installment     = ($request->getInstallment()) ? $this->formatInstallment($request->getInstallment()) : null;
        $amount          = $this->isAmountRequired($request) ? $this->formatAmount($request->getAmount()) : '1';
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

    /**
     * returns boolean true, when amount field is required
     * for request transaction type.
     *
     * @param Request $request
     * @return boolean
     */
    private function isAmountRequired(Request $request)
    {
        return in_array(
            $request->getTransactionType(),
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
     * @param Request $request
     * @return boolean
     */
    private function isCardNumberRequired(Request $request)
    {
        return in_array(
            $request->getTransactionType(),
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
        $config = $this->config;
        if ($isAuth) {
            return array( $config->auth_username, $config->auth_password );
        } else {
            return array( $config->refund_username, $config->refund_password );
        }
    }

    /**
     * returns security hash for using in transaction hash.
     *
     * @param string $password
     * @return string
     */
    private function getSecurityHash($password)
    {
        $config     = $this->config;
        $tidPrefix  = str_repeat('0', 9 - strlen($config->terminal_id));
        $terminalId = sprintf('%s%s', $tidPrefix, $config->terminal_id);
        return strtoupper(SHA1(sprintf('%s%s', $password, $terminalId)));
    }

    /**
     * returns transaction hash for using in transaction request.
     *
     * @param Request $request
     * @param string  $password
     * @return string
     */
    private function getTransactionHash(Request $request, $password)
    {
        $config       = $this->config;
        $orderId      = $request->getOrderId();
        $terminalId   = $config->terminal_id;
        $cardNumber   = $this->isCardNumberRequired($request) ? $request->getCardNumber() : '';
        $amount       = $this->isAmountRequired($request) ? $this->formatAmount($request->getAmount()) : '1';
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
        $data       = array( 'data' => $xml );
        $request->setRawData($xml);
        return http_build_query($data);
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildPreauthorizationRequest()
     */
    protected function buildPreAuthorizationRequest(Request $request)
    {
        $requestData = array( 'Card' => $this->buildCard($request) );
        return array_merge($requestData, $this->buildBaseRequest($request));
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildPostAuthorizationRequest()
     */
    protected function buildPostAuthorizationRequest(Request $request)
    {
        $requestData = $this->buildBaseRequest($request);
        return $requestData;
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildSaleRequest()
     */
    protected function buildSaleRequest(Request $request)
    {
        $requestData = array( 'Card' => $this->buildCard($request) );
        return array_merge($requestData, $this->buildBaseRequest($request));
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildRefundRequest()
     */
    protected function buildRefundRequest(Request $request)
    {
        return $this->buildBaseRequest($request);
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildCancelRequest()
     */
    protected function buildCancelRequest(Request $request)
    {
        $requestData                = $this->buildBaseRequest($request);
        $transactionId              = ($request->getTransactionId()) ? $request->getTransactionId() : null;
        $transaction                = $this->buildTransaction($request, 0, $transactionId);
        $requestData['Transaction'] = $transaction;
        return $requestData;
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::parseResponse()
     */
    protected function buildPointQueryRequest(Request $request)
    {
        $exception = new UnimplementedMethod('Provider method not implemented: ' . $request->getTransactionType());
        $this->triggerEvent(self::EVENT_ON_EXCEPTION, array( 'exception' => $exception ));
        throw $exception;
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildPointUsageRequest()
     */
    protected function buildPointUsageRequest(Request $request)
    {
        $exception = new UnimplementedMethod('Provider method not implemented: ' . $request->getTransactionType());
        $this->triggerEvent(self::EVENT_ON_EXCEPTION, array( 'exception' => $exception ));
        throw $exception;
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::parseResponse()
     */
    protected function parseResponse($rawResponse)
    {
        $response = new PaymentResponse();
        try {
            /**
             * @var object $xml
             */
            $xml = new \SimpleXmlElement($rawResponse);
        } catch ( \Exception $e ) {
            throw new UnexpectedResponse('Provider returned unexpected response: ' . $rawResponse);
        }
        $response->setIsSuccess((string)$xml->Transaction->Response->Code == '00');
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
        $response->setRawData($rawResponse);
        return $response;
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::formatAmount()
     */
    protected function formatAmount($amount, $reverse = false)
    {
        if (!$reverse) {
            return number_format($amount, 2, '', '');
        } else {
            return (float)sprintf('%s.%s', substr($amount, 0, -2), substr($amount, -2));
        }
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::formatExpireDate()
     */
    protected function formatExpireDate($month, $year)
    {
        return sprintf('%02s%s', $month, substr($year, -2));
    }
}
