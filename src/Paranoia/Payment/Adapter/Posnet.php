<?php
namespace Paranoia\Payment\Adapter;

use Paranoia\Common\Serializer\Serializer;
use Paranoia\Payment\Request;
use Paranoia\Payment\Response\PaymentResponse;
use Paranoia\Payment\Exception\UnexpectedResponse;
use Paranoia\Payment\Exception\UnimplementedMethod;
use Paranoia\Communication\Connector;

class Posnet extends AdapterAbstract implements AdapterInterface
{

    const CONNECTOR_TYPE = Connector::CONNECTOR_TYPE_HTTP;
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

    /**
     * builds request base with common arguments.
     *
     * @return array
     */
    private function buildBaseRequest()
    {
        $config = $this->config;
        return array(
            'username' => $config->username,
            'password' => $config->password,
            'mid'      => $config->client_id,
            'tid'      => $config->terminal_id
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
            array( 'root_name' => 'posnetRequest' )
        );
        $data       = array( 'xmldata' => $xml );
        $request->setRawData($xml);
        return http_build_query($data);
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildPreauthorizationRequest()
     */
    protected function buildPreauthorizationRequest(Request $request)
    {
        $amount      = $this->formatAmount($request->getAmount());
        $installment = $this->formatInstallment($request->getInstallment());
        $currency    = $this->formatCurrency($request->getCurrency());
        $expireMonth = $this->formatExpireDate($request->getExpireMonth(), $request->getExpireYear());
        $type        = $this->getProviderTransactionType($request->getTransactionType());
        $requestData = array(
            $type => array(
                'ccno'          => $request->getCardNumber(),
                'expDate'       => $expireMonth,
                'cvc'           => $request->getSecurityCode(),
                'amount'        => $amount,
                'currencyCode'  => $currency,
                'orderID'       => $request->getOrderId(),
                'installment'   => $installment,
                'extraPoint'    => "000000",
                'multiplePoint' => "000000"
            )
        );
        return $requestData;
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildPostAuthorizationRequest()
     */
    protected function buildPostAuthorizationRequest(Request $request)
    {
        $amount      = $this->formatAmount($request->getAmount());
        $installment = $this->formatInstallment($request->getInstallment());
        $currency    = $this->formatCurrency($request->getCurrency());
        $type        = $this->getProviderTransactionType($request->getTransactionType());
        $requestData = array(
            $type => array(
                'hostLogKey'    => $request->getTransactionId(),
                'authCode'      => $request->getAuthCode(),
                'amount'        => $amount,
                'currencyCode'  => $currency,
                'installment'   => $installment,
                'extraPoint'    => "000000",
                'multiplePoint' => "000000"
            )
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
            $type => array(
                'ccno'          => $request->getCardNumber(),
                'expDate'       => $expireMonth,
                'cvc'           => $request->getSecurityCode(),
                'amount'        => $amount,
                'currencyCode'  => $currency,
                'orderID'       => $request->getOrderId(),
                'installment'   => $installment,
                'extraPoint'    => "000000",
                'multiplePoint' => "000000"
            )
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
            $type => array(
                'hostLogKey'   => $request->getTransactionId(),
                'amount'       => $amount,
                'currencyCode' => $currency
            )
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
            $type => array(
                'transaction' => "sale",
                'hostLogKey'  => $request->getTransactionId(),
                'authCode'    => $request->getAuthCode()
            )
        );
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
        $response->setRawData($rawResponse);
        $eventData = $this->collectTransactionInformation();
        $eventName = $response->isSuccess() ? self::EVENT_ON_TRANSACTION_SUCCESSFUL : self::EVENT_ON_TRANSACTION_FAILED;
        $this->triggerEvent($eventName, $eventData);
        return $response;
    }

    /**
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
     * Posnet Son Kullanma Tarihini YYMM formatında istiyor. Örnek:03/2014 için 1403
     *
     * @see Paranoia\Payment\Adapter\AdapterAbstract::formatExpireDate()
     */
    protected function formatExpireDate($month, $year)
    {
        return sprintf('%02s%02s', substr($year, -2), $month);
    }

    /**
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
     * Posnet currency kodları yerine ilk iki harfi istiyor.
     *
     * @see Paranoia\Payment\Adapter\AdapterAbstract::formatCurrency()
     */
    protected function formatCurrency($currency)
    {
        switch ($currency) {
            case self::CURRENCY_TRY:
                return 'YT';
            case self::CURRENCY_USD:
                return 'US';
            case self::CURRENCY_EUR:
                return 'EU';
            default:
                return 'YT';
        }
    }
}
