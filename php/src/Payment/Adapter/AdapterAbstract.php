<?php
namespace Payment\Adapter;

use \Payment\Request;
use \Payment\Response;
use \Payment\Config;

use \Communication\Connector;

abstract class AdapterAbstract
{
    const CURRENCY_TRY = 'TRY';
    const CURRENCY_USD = 'USD';
    const CURRENCY_EUR = 'EUR';

    /**
    * @var \Payment\Config
    */
    protected $_config;

    public function __construct(\Zend_Config $config)
    {
        $this->_config = $config;
    }

    /**
    * build request data for preauthorization transaction.
    *
    * @param \Payment\Request $request
    * @return mixed
    */
    abstract protected function _buildPreauthorizationRequest(Request $request);

    /**
    * build request data for postauthorization transaction.
    *
    * @param \Payment\Request $request
    * @return mixed
    */
    abstract protected function _buildPostAuthorizationRequest(Request $request);

    /**
    * build request data for sale transaction.
    *
    * @param \Payment\Request $request
    * @return mixed
    */
    abstract protected function _buildSaleRequest(Request $request);

    /**
    * build request data for refund transaction.
    *
    * @param \Payment\Request $request
    * @return mixed
    */
    abstract protected function _buildRefundRequest(Request $request);

    /**
    * build request data for cancel transaction.
    *
    * @param \Payment\Request $request
    * @return mixed
    */
    abstract protected function _buildCancelRequest(Request $request);

    /**
    *  build complete raw data for the specified request.
    *
    * @param \Payment\Request
    * @param string $requestBuilder
    * @return mixed
    */
    abstract protected function _buildRequest(Request $request, $requestBuilder);

    /**
    * parses response from returned provider.
    *
    * @param string $rawResponse
    * @return \Payment\Response\PaymentResponse
    */
    abstract protected function _parseResponse($rawResponse);

    /**
     * sends request to remote host.
     *
     * @param string $url
     * @param mixed $data
     * @param array $options
     * @return mixed
     */
    protected function _sendRequest($url, $data, $options=null)
    {
        $connector = new Connector(static::CONNECTOR_TYPE);
        return $connector->sendRequest($url, $data, $options);
    }

    /**
    * formats the specified string currency code by iso currency codes.
    * @param string $currency
    * @return integer
    */
    protected function _formatCurrency($currency)
    {
        switch($currency) {
            case self::CURRENCY_TRY:
                return '949';
            case self::CURRENCY_USD:
                return '840';
            case self::CURRENCY_EUR:
                return '978';
            default:
                return '949';
        }
    }

    /**
    * returns formatted amount with doth or without doth.
    * formatted number returns amount default without doth.
    * @param string/float $amount
    * @param boolean $reverse
    * @return string
    */
    protected function _formatAmount($amount, $reverse = false)
    {
        return (!$reverse) ?
            number_format($amount, 2, '.', '') :
            (float) sprintf('%s.%s', substr($amount, 0, -2),
                                     substr($amount, -2));
    }

    /**
    * formats expire date as month/year
    * @param int $month
    * @param int $year
    * @return string
    */
    protected function _formatExpireDate($month, $year)
    {
        return sprintf('%02s/%04s', $month, $year);
    }

    /**
     * returns formatted installment amount
     * @param int $installment
     * @return string
     */
    protected function _formatInstallment($installment)
    {
        return ( !is_numeric($installment) || intval($installment) <= 1 ) ?
            '' : $installment;
    }

    /**
    * @see AdapterInterface::preAuthorization()
    */
    public function preAuthorization(Request $request)
    {
        $rawRequest = $this->_buildRequest($request, '_buildPreauthorizationRequest');
        $rawResponse = $this->_sendRequest($this->_config->api_url, $rawRequest);
        $response = $this->_parseResponse($rawResponse);
        return $response;
    }


    /**
    * @see AdapterInterface::postAuthorization()
    */
    public function postAuthorization(Request $request)
    {
        $rawRequest = $this->_buildRequest($request, '_buildPostAuthorizationRequest');
        $rawResponse = $this->_sendRequest($this->_config->api_url, $rawRequest);
        $response = $this->_parseResponse($rawResponse);
        return $response;
    }

    /**
    * @see AdapterInterface::sale()
    */
    public function sale(Request $request)
    {
        $rawRequest = $this->_buildRequest($request, '_buildSaleRequest');
        $rawResponse = $this->_sendRequest($this->_config->api_url, $rawRequest);
        $response = $this->_parseResponse($rawResponse);
        return $response;
    }

    /**
    * @see AdapterInterface::refund()
    */
    public function refund(Request $request)
    {
        $rawRequest = $this->_buildRequest($request, '_buildRefundRequest');
        $rawResponse = $this->_sendRequest($this->_config->api_url, $rawRequest);
        $response = $this->_parseResponse($rawResponse);
        return $response;
    }

    /**
    * @see AdapterInterface::cancel()
    */
    public function cancel(Request $request)
    {
        $rawRequest = $this->_buildRequest($request, '_buildCancelRequest');
        $rawResponse = $this->_sendRequest($this->_config->api_url, $rawRequest);
        $response = $this->_parseResponse($rawResponse);
        return $response;
    }
}
