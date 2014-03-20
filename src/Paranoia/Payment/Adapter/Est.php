<?php
namespace Paranoia\Payment\Adapter;

use Paranoia\Common\Serializer\Serializer;
use Paranoia\Communication\Connector;
use Paranoia\Payment\Request;
use Paranoia\Payment\Response\PaymentResponse;
use Paranoia\Payment\Exception\UnexpectedResponse;
use Paranoia\Payment\Exception\UnimplementedMethod;

class Est extends AdapterAbstract implements AdapterInterface
{

    const CONNECTOR_TYPE = Connector::CONNECTOR_TYPE_HTTP;
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
        $config = $this->config;
        return array(
            'Name'     => $config->username,
            'Password' => $config->password,
            'ClientId' => $config->client_id,
            'Mode'     => $config->mode
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
            array_merge($rawRequest, $this->buildBaseRequest()),
            array( 'root_name' => 'CC5Request' )
        );
        $data       = array( 'DATA' => $xml );
        $request->setRawData($xml);
        return http_build_query($data);
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildPreauthorizationRequest()
     */
    protected function buildPreAuthorizationRequest(Request $request)
    {
        $amount      = $this->formatAmount($request->getAmount());
        $installment = $this->formatInstallment($request->getInstallment());
        $currency    = $this->formatCurrency($request->getCurrency());
        $expireMonth = $this->formatExpireDate($request->getExpireMonth(), $request->getExpireYear());
        $type        = $this->getProviderTransactionType($request->getTransactionType());
        $requestData = array(
            'Type'     => $type,
            'Total'    => $amount,
            'Currency' => $currency,
            'Taksit'   => $installment,
            'Number'   => $request->getCardNumber(),
            'Cvv2Val'  => $request->getSecurityCode(),
            'Expires'  => $expireMonth,
            'OrderId'  => $request->getOrderId(),
        );
        return $requestData;
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildPostAuthorizationRequest()
     */
    protected function buildPostAuthorizationRequest(Request $request)
    {
        $type        = $this->getProviderTransactionType($request->getTransactionType());
        $requestData = array(
            'Type'    => $type,
            'OrderId' => $request->getOrderId(),
        );
        return $requestData;
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildSaleRequest()
     */
    protected function buildSaleRequest(Request $request)
    {
        $amount      = $this->formatAmount($request->getAmount());
        $installment = $this->formatInstallment($request->getInstallment());
        $currency    = $this->formatCurrency($request->getCurrency());
        $expireMonth = $this->formatExpireDate($request->getExpireMonth(), $request->getExpireYear());
        $type        = $this->getProviderTransactionType($request->getTransactionType());
        $requestData = array(
            'Type'     => $type,
            'Total'    => $amount,
            'Currency' => $currency,
            'Taksit'   => $installment,
            'Number'   => $request->getCardNumber(),
            'Cvv2Val'  => $request->getSecurityCode(),
            'Expires'  => $expireMonth,
            'OrderId'  => $request->getOrderId(),
        );
        return $requestData;
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildRefundRequest()
     */
    protected function buildRefundRequest(Request $request)
    {
        $amount      = $this->formatAmount($request->getAmount());
        $currency    = $this->formatCurrency($request->getCurrency());
        $type        = $this->getProviderTransactionType($request->getTransactionType());
        $requestData = array(
            'Type'     => $type,
            'Total'    => $amount,
            'Currency' => $currency,
            'OrderId'  => $request->getOrderId(),
        );
        return $requestData;
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildCancelRequest()
     */
    protected function buildCancelRequest(Request $request)
    {
        $type        = $this->getProviderTransactionType($request->getTransactionType());
        $requestData = array(
            'Type'    => $type,
            'OrderId' => $request->getOrderId(),
        );
        if ($request->getTransactionId()) {
            $requestData['TransId'] = $request->getTransactionId();
        }
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
            $exception = new UnexpectedResponse('Provider returned unexpected response: ' . $rawResponse);
            $this->triggerEvent(
                self::EVENT_ON_EXCEPTION,
                array_merge(
                    $this->collectTransactionInformation(),
                    array( 'exception' => $exception )
                )
            );
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
        $response->setRawData($rawResponse);
        $eventData = $this->collectTransactionInformation();
        $eventName = $response->isSuccess() ? self::EVENT_ON_TRANSACTION_SUCCESSFUL : self::EVENT_ON_TRANSACTION_FAILED;
        $this->triggerEvent($eventName, $eventData);
        return $response;
    }
}
