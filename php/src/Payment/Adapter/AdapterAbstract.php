<?php
namespace Payment\Adapter;

use \Payment\Request;
use \Payment\Response;
use \Payment\Config;

use \Communication\Connector;

use \EventManager\EventManagerAbstract;

abstract class AdapterAbstract extends EventManagerAbstract
{
    const CURRENCY_TRY = 'TRY';
    const CURRENCY_USD = 'USD';
    const CURRENCY_EUR = 'EUR';

    const EVENT_ON_TRANSACTION_SUCCESSFUL = 'OnTransactionSuccessful';
    const EVENT_ON_TRANSACTION_FAILED = 'OnTransactionFailed';
    const EVENT_ON_EXCEPTION = 'OnException';
    /**
    * @var \Payment\Config
    */
    protected $_config;

    /**
     * @var \Communication\Adapter\CommunicationInterface
     */
    protected $_connector;

    public function __construct(\Zend_Config $config)
    {
        $this->_config = $config;
        $this->_connector = new Connector(static::CONNECTOR_TYPE);
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
     * returns connector object.
     *
     * @return \Communication\Adapter\AdapterInterface
     */
    public function getConnector()
    {
        return $this->_connector;
    }

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
        try {
            return $this->getConnector()->sendRequest($url, $data, $options);
        } catch(\ErrorException $e) {
            $backtrace = debug_backtrace();
            $this->_triggerEvent(self::EVENT_ON_EXCEPTION,
                                 array('exception' => $e,
                                       'request'   => $this->_maskRequest($this->getConnector()->getLastSentRequest()),
                                       'response'  => $this->getConnector()->getLastReceivedResponse()));
            throw $e;
        }
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

    /**
     * mask some critical information in transaction request.
     *
     * @param string $rawRequest
     * @return string
     */
    protected function _maskRequest($rawRequest)
    {
        return $rawRequest;
    }

    /**
     * collects transaction information.
     *
     * @return array
     */
    protected function _collectTransactionInformation()
    {
        $backtrace = debug_backtrace();
        $data = array('transaction' => $backtrace[2]['function'],
                      'request'     => $this->_maskRequest($this->getConnector()->getLastSentRequest()),
                      'response'    => $this->getConnector()->getLastReceivedResponse(),);
        return $data;
    }
}
