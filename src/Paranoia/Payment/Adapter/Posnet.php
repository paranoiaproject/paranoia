<?php
namespace Paranoia\Payment\Adapter;

use Paranoia\Common\Serializer\Serializer;
use Paranoia\Payment\PaymentEventArg;
use Paranoia\Payment\Request;
use Paranoia\Payment\ConfirmRequest;
use Paranoia\Payment\Response\PaymentResponse;
use Paranoia\Payment\Exception\UnexpectedResponse;
use Paranoia\Payment\Exception\UnimplementedMethod;

class Posnet extends AdapterAbstract
{
    /**
     * @var array
     */
    protected $currencyCodes = array(
        self::CURRENCY_TRY => 'YT',
        self::CURRENCY_EUR => 'EU',
        self::CURRENCY_USD => 'US',
    );

    /**
     * @var array
     */
    protected $transactionMap = array(
        self::TRANSACTION_TYPE_PREAUTHORIZATION  => 'auth',
        self::TRANSACTION_TYPE_POSTAUTHORIZATION => 'capt',
        self::TRANSACTION_TYPE_SALE              => 'sale',
        self::TRANSACTION_TYPE_SALE_3D           => 'Sale',
        self::TRANSACTION_TYPE_CANCEL            => 'reverse',
        self::TRANSACTION_TYPE_REFUND            => 'return',
        self::TRANSACTION_TYPE_POINT_QUERY       => 'pointinquiry',
        self::TRANSACTION_TYPE_POINT_USAGE       => 'pointusage',
    );

    /**
     * builds request base with common arguments.
     *
     * @return array
     */
    private function buildBaseRequest($is3D = null)
    {
        return array(
            'mid' => $this->configuration->getMerchantId(),
            'tid' => !$is3D ? $this->configuration->getTerminalId() :
                     $this->configuration->getTerminal3DId()
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
            array_merge($this->buildBaseRequest(), $rawRequest),
            array( 'root_name' => 'posnetRequest' )
        );
        return array( 'xmldata' => $xml );
    }

    protected function buildConfirmRequest(ConfirmRequest $confirmRequest, $requestBuilder)
    {
        $rawRequest = call_user_func(array( $this, $requestBuilder ), $confirmRequest);
        $serializer = new Serializer(Serializer::XML);
        $xml        = $serializer->serialize(
            array_merge($this->buildBaseRequest(true), $rawRequest),
            array( 'root_name' => 'posnetRequest' )
        );
        return array( 'xmldata' => $xml );
    }

    protected function buildRawRequest($request, $requestBuilder)
    {
        $rawRequest = call_user_func(array( $this, $requestBuilder ), $request);
        $serializer = new Serializer(Serializer::XML);
        $xml        = $serializer->serialize(
            array_merge($this->buildBaseRequest(true), $rawRequest),
            array( 'root_name' => 'posnetRequest' )
        );
        return array( 'xmldata' => $xml );
    }

    public function sale3D(Request $request)
    {
        $rawRequest  = $this->buildRequest($request, 'buildSale3DRequest');
        $rawResponse = $this->sendRequest($this->configuration->getApiUrl(), $rawRequest);
        $response    = $this->parse3DRequestResponse($rawResponse);

        if(! $response->isSuccess()) {
            return $response;
        }

        $rawRequest  = $this->buildRedirect3DRequest($response);
        $rawResponse = $this->sendRequest($this->configuration->getApi3DUrl(), $rawRequest);
        return $rawResponse->__toString();
    }

    public function confirm3D(ConfirmRequest $confirmRequest)
    {
        $request     = $confirmRequest->getRequest();
        $payload     = $confirmRequest->getPayload();

        $rawRequest  = $this->buildRawRequest($payload, 'buildSale3DResolveRequest');
        $rawResponse = $this->sendRequest($this->configuration->getApiUrl(), $rawRequest);
        $response    = $this->parse3DResolveResponse($rawResponse);

        if(! $response->isSuccess()) {
            return $response;
        }

        $mdStatus = $response->getMdStatus();

        $rawRequest  = $this->buildRawRequest($payload, 'buildSale3DTranRequest');
        $rawResponse = $this->sendRequest($this->configuration->getApiUrl(), $rawRequest);
        $response    =  $this->parseResponse($rawResponse, self::TRANSACTION_TYPE_SALE);
        $response->setMdStatus($mdStatus);

        return $response;
    }

    public function build3DRequest(Request $request, $requestBuilder)
    {
        // pass
    }

    protected function buildRedirect3DRequest($response)
    {
        $data = $response->getData();

        $requestData = array(
            'mid'               => $this->configuration->getMerchantId(),
            'posnetID'          => $this->configuration->getPosnetId(),
            'posnetData'        => $data['data1'],
            'posnetData2'       => $data['data2'],
            'digest'            => $data['sign'],
            'merchantReturnURL' => $this->configuration->getSuccessUrl(),
            'lang'              => 'tr',
            'url'               => $this->configuration->getErrorUrl(),
            'openANewWindow'    => '0',
        );

        return $requestData;
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildPreauthorizationRequest()
     */
    protected function buildPreauthorizationRequest(Request $request)
    {
        $cardNumber  = $this->formatCardNumber($request->getCardNumber());
        $amount      = $this->formatAmount($request->getAmount());
        $installment = $this->formatInstallment($request->getInstallment());
        $currency    = $this->formatCurrency($request->getCurrency());
        $expireMonth = $this->formatExpireDate($request->getExpireMonth(), $request->getExpireYear());
        $type        = $this->getProviderTransactionType(self::TRANSACTION_TYPE_PREAUTHORIZATION);
        $requestData = array(
            $type => array(
                'ccno'          => $cardNumber,
                'expDate'       => $expireMonth,
                'cvc'           => $request->getSecurityCode(),
                'amount'        => $amount,
                'currencyCode'  => $currency,
                'orderID'       => $this->formatOrderId($request->getOrderId()),
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
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildPostAuthorizationRequest()
     */
    protected function buildPostAuthorizationRequest(Request $request)
    {
        $amount      = $this->formatAmount($request->getAmount());
        $installment = $this->formatInstallment($request->getInstallment());
        $currency    = $this->formatCurrency($request->getCurrency());
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
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildSaleRequest()
     */
    protected function buildSaleRequest(Request $request)
    {
        $cardNumber  = $this->formatCardNumber($request->getCardNumber());
        $amount      = $this->formatAmount($request->getAmount());
        $installment = $this->formatInstallment($request->getInstallment());
        $currency    = $this->formatCurrency($request->getCurrency());
        $expireMonth = $this->formatExpireDate($request->getExpireMonth(), $request->getExpireYear());
        $type        = $this->getProviderTransactionType(self::TRANSACTION_TYPE_SALE);
        $requestData = array(
            $type => array(
                'ccno'          => $cardNumber,
                'expDate'       => $expireMonth,
                'cvc'           => $request->getSecurityCode(),
                'amount'        => $amount,
                'currencyCode'  => $currency,
                'orderID'       => $this->formatOrderId($request->getOrderId()),
                'installment'   => $installment,
                #TODO: this fields will be used, when point and some bank benefit usage is implemented.
                // 'extraPoint'    => "000000",
                // 'multiplePoint' => "000000"
            )
        );

        return $requestData;
    }

    protected function buildSale3DRequest(Request $request)
    {
        $cardNumber  = $this->formatCardNumber($request->getCardNumber());
        $amount      = $this->formatAmount($request->getAmount());
        $installment = $this->formatInstallment($request->getInstallment());
        $currency    = $this->formatCurrency($request->getCurrency());
        $expireDate  = $this->formatExpireDate($request->getExpireMonth(), $request->getExpireYear());
        $type        = $this->getProviderTransactionType(self::TRANSACTION_TYPE_SALE_3D);

        $requestData = array(
            'oosRequestData' => array(
                'posnetid'       => $this->configuration->getPosnetId(),
                'ccno'           => $cardNumber,
                'expDate'        => $expireDate,
                'cvc'            => $request->getSecurityCode(),
                'amount'         => $amount,
                'currencyCode'   => $currency,
                'installment'    => $installment,
                'XID'            => $request->getOrderId(),
                'cardHolderName' => $request->getCardHolderName(),
                'tranType'       => $type
            )
        );

        return $requestData;
    }

    protected function buildSale3DResolveRequest($payload)
    {
        return array(
            'oosResolveMerchantData' => array(
                'bankData'     => $payload['BankPacket'],
                'merchantData' => $payload['MerchantPacket'],
                'sign'         => $payload['Sign']
            )
        );
    }

    protected function buildSale3DTranRequest($payload)
    {
        return array(
            'oosTranData' => array(
                'bankData' => $payload['BankPacket']
            )
        );
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildRefundRequest()
     */
    protected function buildRefundRequest(Request $request)
    {
        $amount      = $this->formatAmount($request->getAmount());
        $currency    = $this->formatCurrency($request->getCurrency());
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
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildCancelRequest()
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
        $response->setIsSuccess((int)$xml->approved == 1);
        $response->setRawResponse($xml);
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
                $response->setAuthCode((string)$xml->authCode);
            }
        }
        $event = $response->isSuccess() ? self::EVENT_ON_TRANSACTION_SUCCESSFUL : self::EVENT_ON_TRANSACTION_FAILED;
        $this->getDispatcher()->dispatch($event, new PaymentEventArg(null, $response, $transactionType));
        return $response;
    }

    public function parse3DResolveResponse($rawResponse) {

        $response = new PaymentResponse();

        try {
            $xml = simplexml_load_string($rawResponse);

            $approved   = (int) $xml->approved;
            $orderId    = (string) $xml->oosResolveMerchantDataResponse->xid;
            $errorMsg   = (string) $xml->respText;
            $mdStatus   = (int) $xml->oosResolveMerchantDataResponse->mdStatus;
            $mdErrorMsg = (string) $xml->oosResolveMerchantDataResponse->mdErrorMessage;

            // NOTE: $mdStatus should be 9 for success in test env.
            $response->setIsSuccess($approved == 1 && $mdStatus == 1);
            $response->setOrderId($orderId);
            $response->setMdStatus($mdStatus);
            $response->setResponseCode($xml->respCode);
            $response->setResponseMessage(sprintf('%s - %s', utf8_encode($errorMsg), $mdErrorMsg));
        } catch(Exception $e) {
            $response->setIsSuccess(false);
            $response->setResponseMessage($e->getMessage());
        }

        $response->setRawResponse(utf8_encode($rawResponse));

        return $response;
    }

    protected function parse3DRequestResponse($rawResponse)
    {
        $response = new PaymentResponse();

        try {
            $resultObject  = simplexml_load_string($rawResponse);

            $approved    = (string) $resultObject->approved;
            $respCode    = (string) $resultObject->respCode;
            $respMessage = (string) $resultObject->respText;

            $response->setIsSuccess($approved == 1);

            if($approved == 1) {
                $data1 = (string) $resultObject->oosRequestDataResponse->data1;
                $data2 = (string) $resultObject->oosRequestDataResponse->data2;
                $sign  = (string) $resultObject->oosRequestDataResponse->sign;

                $data = [
                    'data1' => $data1,
                    'data2' => $data2,
                    'sign'  => $sign
                ];

                $response->setData($data);
            }

            $response->setResponseCode($respCode);
            $response->setResponseMessage(utf8_encode($respMessage));
        } catch(Exception $e){
            $response->setIsSuccess(false);
            $response->setResponseMessage($e->getMessage());
        }

        $response->setRawResponse(utf8_encode($rawResponse));

        return $response;
    }

    protected function parseBank3DResponse($rawResponse)
    {
        $response = new PaymentResponse();

        $response->setOrderId($rawResponse['Xid']);
        $response->setTransactionId($rawResponse['Xid']);

        // IMPORTANT! in this step, Posnet hasn't sent mdStatus to us yet.
        // so assume that mdStatus is 1 to continue.
        $response->setMdStatus(1);

        // extraData is not currently using.
        $extraData = [
            'bankPacket'     => $rawResponse['BankPacket'],
            'merchantPacket' => $rawResponse['MerchantPacket'],
            'sign'           => $rawResponse['Sign'],
        ];
        $response->setData($extraData);

        return $response;
    }

    public function check3DHashIntegrity($payload)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     * Posnet tutar değerinde nokta istemiyor. Örnek:15.00TL için 1500 gönderilmesi gerekiyor.
     *
     * @see Paranoia\Payment\Adapter\AdapterAbstract::formatAmount()
     */
    protected function formatAmount($amount, $reverse = false)
    {
        if (!$reverse) {
            return ceil($amount * 100);
        } else {
            return (float)sprintf('%s.%s', substr($amount, 0, -2), substr($amount, -2));
        }
    }

    /**
     * {@inheritdoc}
     * Posnet Son Kullanma Tarihini YYMM formatında istiyor. Örnek:03/2014 için 1403
     *
     * @see Paranoia\Payment\Adapter\AdapterAbstract::formatExpireDate()
     */
    protected function formatExpireDate($month, $year)
    {
        return sprintf('%02s%02s', substr($year, -2), $month);
    }

    /**
     * {@inheritdoc}
     * Postnet Taksit sayısında daima 2 rakam gönderilmesini istiyor.
     *
     * @see Paranoia\Payment\Adapter\AdapterAbstract::formatInstallment()
     */
    protected function formatInstallment($installment)
    {
        if (!is_numeric($installment) || intval($installment) <= 1) {
            return '00';
        }
        return sprintf('%02s', $installment);
    }

    /**
     * @param $orderId
     * @return mixed|string
     */
    protected function formatOrderId($orderId)
    {
        return str_repeat('0', 24 - strlen($orderId)) . $orderId;
    }
}
