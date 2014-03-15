<?php
namespace Paranoia\Payment\Adapter;

use Paranoia\Common\Serializer\Serializer;
use Paranoia\Payment\Request;
use Paranoia\Payment\Response\PaymentResponse;
use Paranoia\Payment\Exception\UnexpectedResponse;
use Paranoia\Communication\Connector;

class Posnet extends AdapterAbstract implements AdapterInterface
{

    const CONNECTOR_TYPE = Connector::CONNECTOR_TYPE_HTTP;
    /**
     * @var array
     */
    protected $_transactionMap = array(
        self::TRANSACTION_TYPE_PREAUTHORIZATION  => 'auth',
        self::TRANSACTION_TYPE_POSTAUTHORIZATION => 'capt',
        self::TRANSACTION_TYPE_SALE              => 'sale',
        self::TRANSACTION_TYPE_CANCEL            => 'reverse',
        self::TRANSACTION_TYPE_REFUND            => 'return'
    );

    /**
     * builds request base with common arguments.
     *
     * @return array
     */
    private function _buildBaseRequest()
    {
        $config = $this->_config;
        return array(
            'username' => $config->username,
            'password' => $config->password,
            'mid'      => $config->client_id,
            'tid'      => $config->terminal_id
        );
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::_buildRequest()
     */
    protected function _buildRequest( Request $request, $requestBuilder )
    {
        $rawRequest = call_user_func(array( $this, $requestBuilder ), $request);
        $serializer = new Serializer( Serializer::XML );
        $xml        = $serializer->serialize(
                                 array_merge($rawRequest, $this->_buildBaseRequest()),
                                     array( 'root_name' => 'posnetRequest' )
        );
        $data       = array( 'xmldata' => $xml );
        $request->setRawData($xml);
        return http_build_query($data);
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::_buildPreauthorizationRequest()
     */
    protected function _buildPreauthorizationRequest( Request $request )
    {
        $amount      = $this->_formatAmount($request->getAmount());
        $installment = $this->_formatInstallment($request->getInstallment());
        $currency    = $this->_formatCurrency($request->getCurrency());
        $expireMonth = $this->_formatExpireDate($request->getExpireMonth(), $request->getExpireYear());
        $type        = $this->_getProviderTransactionType($request->getTransactionType());
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
     * @see Paranoia\Payment\Adapter\AdapterAbstract::_buildPostAuthorizationRequest()
     */
    protected function _buildPostAuthorizationRequest( Request $request )
    {
        $amount      = $this->_formatAmount($request->getAmount());
        $installment = $this->_formatInstallment($request->getInstallment());
        $currency    = $this->_formatCurrency($request->getCurrency());
        $type        = $this->_getProviderTransactionType($request->getTransactionType());
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
     * @see Paranoia\Payment\Adapter\AdapterAbstract::_buildSaleRequest()
     */
    protected function _buildSaleRequest( Request $request )
    {
        $amount      = $this->_formatAmount($request->getAmount());
        $installment = $this->_formatInstallment($request->getInstallment());
        $currency    = $this->_formatCurrency($request->getCurrency());
        $expireMonth = $this->_formatExpireDate($request->getExpireMonth(), $request->getExpireYear());
        $type        = $this->_getProviderTransactionType($request->getTransactionType());
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
     * @see Paranoia\Payment\Adapter\AdapterAbstract::_buildRefundRequest()
     */
    protected function _buildRefundRequest( Request $request )
    {
        $amount      = $this->_formatAmount($request->getAmount());
        $currency    = $this->_formatCurrency($request->getCurrency());
        $type        = $this->_getProviderTransactionType($request->getTransactionType());
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
     * @see Paranoia\Payment\Adapter\AdapterAbstract::_buildCancelRequest()
     */
    protected function _buildCancelRequest( Request $request )
    {
        $type        = $this->_getProviderTransactionType($request->getTransactionType());
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
     * @see Paranoia\Payment\Adapter\AdapterAbstract::_parseResponse()
     */
    protected function _parseResponse( $rawResponse )
    {
        $response = new PaymentResponse();
        try {
            $xml = (object)new \SimpleXmlElement( $rawResponse );
        } catch ( \Exception $e ) {
            $exception = new UnexpectedResponse( 'Provider is returned unexpected response. Response data:' . $rawResponse );
            $this->_triggerEvent(
                 self::EVENT_ON_EXCEPTION,
                     array_merge(
                         $this->_collectTransactionInformation(),
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
        $eventData = $this->_collectTransactionInformation();
        $eventName = $response->isSuccess() ? self::EVENT_ON_TRANSACTION_SUCCESSFUL : self::EVENT_ON_TRANSACTION_FAILED;
        $this->_triggerEvent($eventName, $eventData);
        return $response;
    }

    /**
     * Posnet Son Kullanma Tarihini YYMM formatında istiyor. Örnek:03/2014 için 1403
     *
     * @see Paranoia\Payment\Adapter\AdapterAbstract::_formatExpireDate()
     */
    protected function _formatExpireDate( $month, $year )
    {
        return sprintf('%02s%02s', substr($year, -2), $month);
    }

    /**
     * Postnet Taksit sayısında daima 2 rakam gönderilmesini istiyor.
     *
     * @see Paranoia\Payment\Adapter\AdapterAbstract::_formatInstallment()
     */
    protected function _formatInstallment( $installment )
    {
        if (!is_numeric($installment) || intval($installment) <= 1) {
            return '00';
        }
        return sprintf('%02s', $installment);
    }

    protected function _formatCurrency( $currency )
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
