<?php
namespace Payment\Adapter;

use \Payment\Request;
use \Payment\Response;
use \Payment\Config;

abstract class AdapterAbstract 
{
    /**
    * @var \Payment\Config
    */
    protected $_config;

    public function __construct(\Zend_Config $config)
    {
        $this->_config = $config;
    }
    
    /**
    * @param \Payment\Request $request
    * @return mixed
    */
    abstract protected function _buildPreauthorizationRequest(Request $request);
    
    /**
    * @param \Payment\Request $request
    * @return mixed
    */
    abstract protected function _buildPostAuthorizationRequest(Request $request);
    
    /**
    * @param \Payment\Request $request
    * @return mixed
    */
    abstract protected function _buildSaleRequest(Request $request);
    
    /**
    * @param \Payment\Request $request
    * @return mixed
    */
    abstract protected function _buildRefundRequest(Request $request);
    
    /**
    * @param \Payment\Request $request
    * @return mixed
    */
    abstract protected function _buildCancelRequest(Request $request);
    
    /**
    * @param \Payment\Request $request
    * @return mixed
    */
    abstract protected function _buildInquiryRequest(Request $request);
    
    /**
    *  build complete raw data for the specified request.
    *
    * @param \Payment\Request
    * @param string $requestBuilder
    * @return mixed
    */
    abstract protected function _buildRequest(Request $request, $requestBuilder);

    /**
    * @param string $rawResponse
    * @return \Payment\Response\ResponseInterface
    */
    abstract protected function _parseResponse($rawResponse);
    
    /**
    * @see \Payment\Adapter\Container\ContainerAbstract::_sendRequest()
    */
    abstract protected function _sendRequest($url, $rawRequest, $options = array());
    
    /**
    * formats the specified string currency code by iso currency codes.
    * @param string $currency
    * @return integer
    */
    protected function _formatCurrency($currency)
    {
        return '949';
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
        $rawRequest = $this->_buildRequest($request, 
                                            $this->_buildPreauthorizationRequest);
        $rawResponse = $this->_sendRequest($this->_config->api_url, $rawRequest);
        $response = $this->_parseResponse($rawResponse);
        return $response;
    }
    
    /**
    * @see AdapterInterface::postAuthorization()
    */
    public function postAuthorization(Request $request)
    {
        $rawRequest = $this->_buildRequest($request, 
                                            $this->_buildPostAuthorizationRequest);
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
    
    /**
    * @see AdapterInterface::inquiry()
    */
    public function inquiry(Request $request)
    {
        $rawRequest = $this->_buildRequest($request, '_buildInquiryRequest');
        $rawResponse = $this->_sendRequest($this->_config->api_url, $rawRequest);
        $response = $this->_parseResponse($rawResponse);
        return $response;
    }
}   
