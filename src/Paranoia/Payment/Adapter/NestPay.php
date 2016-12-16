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

class NestPay extends AdapterAbstract
{
    /**
     * @var array
     */
    protected $transactionMap = array(
        self::TRANSACTION_TYPE_PREAUTHORIZATION  => 'PreAuth',
        self::TRANSACTION_TYPE_POSTAUTHORIZATION => 'PostAuth',
        self::TRANSACTION_TYPE_SALE              => 'Auth',
        self::TRANSACTION_TYPE_SALE_3D           => 'Auth',
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

    protected function get3DTransactionHash($orderId, $amount, $randomKey)
    {
        $hashData = sprintf('%s%s%s%s%s%s%s',
            $this->configuration->getClientId(),
            $orderId,
            $amount,
            $this->configuration->getSuccessUrl(),
            $this->configuration->getErrorUrl(),
            $randomKey,
            $this->configuration->getStoreKey()
        );

        return base64_encode(pack("H*",sha1($hashData)));
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
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildRequest()
     */
    protected function buildConfirmRequest(ConfirmRequest $confirmRequest, $requestBuilder)
    {
        $rawRequest = call_user_func(array( $this, $requestBuilder ), $confirmRequest);
        $serializer = new Serializer(Serializer::XML);
        $xml        = $serializer->serialize(
            array_merge($rawRequest, $this->buildBaseRequest()),
            array( 'root_name' => 'CC5Request' )
        );
        return array( 'DATA' => $xml );
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
        $amount      = $this->formatAmount($request->getAmount());
        $installment = $this->formatInstallment($request->getInstallment());
        $currency    = $this->formatCurrency($request->getCurrency());
        $expireMonth = $this->formatExpireDate($request->getExpireMonth(), $request->getExpireYear());
        $type        = $this->getProviderTransactionType(self::TRANSACTION_TYPE_PREAUTHORIZATION);
        $requestData = array(
            'Type'     => $type,
            'Total'    => $amount,
            'Currency' => $currency,
            'Taksit'   => $installment,
            'Number'   => $this->formatCardNumber($request->getCardNumber()),
            'Cvv2Val'  => $request->getSecurityCode(),
            'Expires'  => $expireMonth,
            'OrderId'  => $this->formatOrderId($request->getOrderId()),
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
            'OrderId' => $this->formatOrderId($request->getOrderId()),
        );
        return $requestData;
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildSaleRequest()
     */
    protected function buildSaleRequest(Request $request)
    {
        $amount      = $this->formatAmount($request->getAmount());
        $installment = $this->formatInstallment($request->getInstallment());
        $currency    = $this->formatCurrency($request->getCurrency());
        $expireMonth = $this->formatExpireDate($request->getExpireMonth(), $request->getExpireYear());
        $type        = $this->getProviderTransactionType(self::TRANSACTION_TYPE_SALE);
        $requestData = array(
            'Type'     => $type,
            'Total'    => $amount,
            'Currency' => $currency,
            'Taksit'   => $installment,
            'Number'   => $this->formatCardNumber($request->getCardNumber()),
            'Cvv2Val'  => $request->getSecurityCode(),
            'Expires'  => $expireMonth,
            'OrderId'  => $this->formatOrderId($request->getOrderId()),
        );
        return $requestData;
    }

    protected function buildSale3DRequest(Request $request)
    {
        $clientId    = $this->configuration->getClientId();
        $orderId     = $this->formatOrderId($request->getOrderId());
        $amount      = $this->formatAmount($request->getAmount());
        $successURL  = $this->configuration->getSuccessUrl();
        $errorURL    = $this->configuration->getErrorUrl();
        $secureCode  = $this->configuration->getStoreKey();
        $cardMonth   = $request->getExpireMonth();
        $cardYear    = $request->getExpireYear();
        $currency    = $this->formatCurrency($request->getCurrency());
        $randomKey   = md5(microtime());

        $hash = $this->get3DTransactionHash($orderId, $amount, $randomKey);

        $requestData = array(
            'clientid'                        => $clientId,
            'storetype'                       => '3d',
            'hash'                            => $hash,
            'pan'                             => $this->formatCardNumber($request->getCardNumber()),
            'amount'                          => $amount,
            'currency'                        => $currency,
            'oid'                             => $orderId,
            'okUrl'                           => $successURL,
            'failUrl'                         => $errorURL,
            'rnd'                             => $randomKey,
            'lang'                            => 'tr',
            'kart_sahibi'                     => $request->getCardHolderName(),
            'Ecom_Payment_Card_ExpDate_Month' => $cardMonth,
            'Ecom_Payment_Card_ExpDate_Year'  => $cardYear,
            'cv2'                             => $request->getSecurityCode()
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
        $orderId     = $this->formatOrderId($request->getOrderId());
        $type        = $this->getProviderTransactionType(self::TRANSACTION_TYPE_SALE_3D);

        $verify = $this->check3DHashIntegrity($payload, $this->configuration->getStoreKey());

        if (! $verify) {
            throw new ResponseVerificationError('Response cannot be verified');
        }

        $requestData = array(
            'Type'                    => $type,
            'IPAddress'               => $request->getIPAddress(),
            'OrderId'                 => $orderId,
            'GroupId'                 => $orderId,
            'Total'                   => $amount,
            'Currency'                => $currency,
            'Number'                  => $payload['md'],
            'Taksit'                  => $installment,
            'PayerSecurityLevel'      => $payload['eci'],
            'PayerTxnId'              => $payload['xid'],
            'PayerAuthenticationCode' => $payload['cavv'],
            'CardholderPresentCode'   => 13
        );

        return $requestData;
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
            'Type'     => $type,
            'Total'    => $amount,
            'Currency' => $currency,
            'OrderId'  => $this->formatOrderId($request->getOrderId()),
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
            'OrderId' => $this->formatOrderId($request->getOrderId()),
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
        $response->setRawResponse($xml);
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
            $response->setAuthCode((string)$xml->AuthCode);
            $response->setTransactionId((string)$xml->TransId);
        }
        $event = $response->isSuccess() ? self::EVENT_ON_TRANSACTION_SUCCESSFUL : self::EVENT_ON_TRANSACTION_FAILED;
        $this->getDispatcher()->dispatch($event, new PaymentEventArg(null, $response, $transactionType));
        return $response;
    }

    protected function parseBank3DResponse($rawResponse)
    {
        $response = new PaymentResponse();
        $response->setOrderId($rawResponse['oid']);
        $response->setTransactionId($rawResponse['oid']);
        $response->setMdStatus($rawResponse['mdStatus']);
        $response->setResponseMessage($rawResponse['mdErrorMsg']);
        return $response;
    }

    public function check3DHashIntegrity($payload) {
        $params   = explode(':', $payload['HASHPARAMS']);
        $storeKey = $this->configuration->getStoreKey();
        $hash     = '';

        foreach($params as $param) {
            if (!empty($param))
                $hash .= !isset($payload[$param]) ? '' : $payload[$param];
        }

        if($this->hashBase64($hash . $storeKey) == $payload['HASH'])
            return true;

        return false;
    }
}
