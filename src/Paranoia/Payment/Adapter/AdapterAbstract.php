<?php
namespace Paranoia\Payment\Adapter;

use Paranoia\Configuration\AbstractConfiguration;
use Paranoia\Payment\Request;
use Paranoia\Payment\Response;
use Paranoia\Payment\TransferInterface;
use Paranoia\Payment\Exception\UnknownTransactionType;
use Paranoia\Communication\Connector;
use Paranoia\EventManager\EventManagerAbstract;

abstract class AdapterAbstract extends EventManagerAbstract
{

    const CURRENCY_TRY                       = 'TRY';
    const CURRENCY_USD                       = 'USD';
    const CURRENCY_EUR                       = 'EUR';
    const EVENT_ON_TRANSACTION_SUCCESSFUL    = 'OnTransactionSuccessful';
    const EVENT_ON_TRANSACTION_FAILED        = 'OnTransactionFailed';
    const EVENT_ON_EXCEPTION                 = 'OnException';
    const TRANSACTION_TYPE_PREAUTHORIZATION  = 'preAuthorization';
    const TRANSACTION_TYPE_POSTAUTHORIZATION = 'postAuthorization';
    const TRANSACTION_TYPE_SALE              = 'sale';
    const TRANSACTION_TYPE_CANCEL            = 'cancel';
    const TRANSACTION_TYPE_REFUND            = 'refund';
    protected $_transactionMap = Array();

    /**
     * @var AbstractConfiguration
     */
    protected $configuration;
    /**
     * @var \Paranoia\Communication\Connector
     */
    protected $_connector;

    public function __construct(AbstractConfiguration $configuration )
    {
        $this->configuration    = $configuration;
        $this->_connector = new Connector( static::CONNECTOR_TYPE );
    }

    /**
     * @param \Paranoia\Configuration\AbstractConfiguration $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return \Paranoia\Configuration\AbstractConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * build request data for preauthorization transaction.
     *
     * @param \Paranoia\Payment\Request $request
     *
     * @return mixed
     */
    abstract protected function _buildPreauthorizationRequest( Request $request );

    /**
     * build request data for postauthorization transaction.
     *
     * @param \Paranoia\Payment\Request $request
     *
     * @return mixed
     */
    abstract protected function _buildPostAuthorizationRequest( Request $request );

    /**
     * build request data for sale transaction.
     *
     * @param \Paranoia\Payment\Request $request
     *
     * @return mixed
     */
    abstract protected function _buildSaleRequest( Request $request );

    /**
     * build request data for refund transaction.
     *
     * @param \Paranoia\Payment\Request $request
     *
     * @return mixed
     */
    abstract protected function _buildRefundRequest( Request $request );

    /**
     * build request data for cancel transaction.
     *
     * @param \Paranoia\Payment\Request $request
     *
     * @return mixed
     */
    abstract protected function _buildCancelRequest( Request $request );

    /**
     *  build complete raw data for the specified request.
     *
     * @param \Paranoia\Payment\Request $request
     * @param string                    $requestBuilder
     *
     * @return mixed
     */
    abstract protected function _buildRequest( Request $request, $requestBuilder );

    /**
     * parses response from returned provider.
     *
     * @param string $rawResponse
     *
     * @return \Paranoia\Payment\Response\PaymentResponse
     */
    abstract protected function _parseResponse( $rawResponse );

    /**
     * returns connector object.
     *
     * @return \Paranoia\Communication\Adapter\AdapterInterface
     */
    public function getConnector()
    {
        return $this->_connector;
    }

    /**
     * sends request to remote host.
     *
     * @param string $url
     * @param mixed  $data
     * @param array  $options
     *
     * @throws \ErrorException
     * @throws \Exception
     * @return mixed
     */
    protected function _sendRequest( $url, $data, $options = null )
    {
        try {
            return $this->getConnector()->sendRequest($url, $data, $options);
        } catch ( \ErrorException $e ) {
            $backtrace = debug_backtrace();
            $this->_triggerEvent(
                 self::EVENT_ON_EXCEPTION,
                     array(
                         'exception' => $e,
                         'request'   => $this->_maskRequest($this->getConnector()->getLastSentRequest()),
                         'response'  => $this->getConnector()->getLastReceivedResponse()
                     )
            );
            throw $e;
        }
    }

    /**
     * formats the specified string currency code by iso currency codes.
     *
     * @param string $currency
     *
     * @return integer
     */
    protected function _formatCurrency( $currency )
    {
        switch ($currency) {
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
     *
     * @param         string /float $amount
     * @param boolean $reverse
     *
     * @return string
     */
    protected function _formatAmount( $amount, $reverse = false )
    {
        if (!$reverse) {
            return number_format($amount, 2, '.', '');
        } else {
            return (float)sprintf('%s.%s', substr($amount, 0, -2), substr($amount, -2));
        }
    }

    /**
     * formats expire date as month/year
     *
     * @param int $month
     * @param int $year
     *
     * @return string
     */
    protected function _formatExpireDate( $month, $year )
    {
        return sprintf('%02s/%04s', $month, $year);
    }

    /**
     * returns formatted installment amount
     *
     * @param int $installment
     *
     * @return string
     */
    protected function _formatInstallment( $installment )
    {
        return ( !is_numeric($installment) || intval($installment) <= 1 ) ? '' : $installment;
    }

    /**
     * stamps transfer objects with time and transaction type.
     *
     * @param \Paranoia\Payment\TransferInterface $transfer
     * @param string                              $transactionType
     */
    private function _stamp( TransferInterface $transfer, $transactionType )
    {
        $transfer->setTime(microtime(true));
        $transfer->setTransactionType($transactionType);
    }

    /**
     * returns transaction code by expected provider.
     *
     * @param string $transactionType
     *
     * @throws \Paranoia\Payment\Exception\UnknownTransactionType
     * @return string
     */
    protected function _getProviderTransactionType( $transactionType )
    {
        if (!array_key_exists($transactionType, $this->_transactionMap)) {
            throw new UnknownTransactionType( 'Transaction type is unknown: ' . $transactionType );
        }
        return $this->_transactionMap[$transactionType];
    }

    /**
     * @see AdapterInterface::preAuthorization()
     *
     * @param \Paranoia\Payment\Request $request
     *
     * @return object
     */
    public function preAuthorization( Request $request )
    {
        $this->_stamp($request, __FUNCTION__);
        $rawRequest  = $this->_buildRequest($request, '_buildPreauthorizationRequest');
        $rawResponse = $this->_sendRequest($this->getConfiguration()->getApiUrl(), $rawRequest);
        $response    = $this->_parseResponse($rawResponse);
        $this->_stamp($response, __FUNCTION__);
        return $response;
    }

    /**
     * @see \Paranoia\Payment\Adapter\AdapterInterface::postAuthorization()
     *
     * @param \Paranoia\Payment\Request $request
     *
     * @return object
     */
    public function postAuthorization( Request $request )
    {
        $this->_stamp($request, __FUNCTION__);
        $rawRequest  = $this->_buildRequest($request, '_buildPostAuthorizationRequest');
        $rawResponse = $this->_sendRequest($this->getConfiguration()->getApiUrl(), $rawRequest);
        $response    = $this->_parseResponse($rawResponse);
        $this->_stamp($response, __FUNCTION__);
        return $response;
    }

    /**
     * @see \Paranoia\Payment\Adapter\AdapterInterface::sale()
     *
     * @param \Paranoia\Payment\Request $request
     *
     * @return object
     */
    public function sale( Request $request )
    {
        $this->_stamp($request, __FUNCTION__);
        $rawRequest  = $this->_buildRequest($request, '_buildSaleRequest');
        $rawResponse = $this->_sendRequest($this->getConfiguration()->getApiUrl(), $rawRequest);
        $response    = $this->_parseResponse($rawResponse);
        $this->_stamp($response, __FUNCTION__);
        return $response;
    }

    /**
     * @see \Paranoia\Payment\Adapter\AdapterInterface::refund()
     *
     * @param \Paranoia\Payment\Request $request
     *
     * @return object
     */
    public function refund( Request $request )
    {
        $this->_stamp($request, __FUNCTION__);
        $rawRequest  = $this->_buildRequest($request, '_buildRefundRequest');
        $rawResponse = $this->_sendRequest($this->getConfiguration()->getApiUrl(), $rawRequest);
        $response    = $this->_parseResponse($rawResponse);
        $this->_stamp($response, __FUNCTION__);
        return $response;
    }

    /**
     * @see \Paranoia\Payment\Adapter\AdapterInterface::cancel()
     *
     * @param \Paranoia\Payment\Request $request
     *
     * @return object
     */
    public function cancel( Request $request )
    {
        $this->_stamp($request, __FUNCTION__);
        $rawRequest  = $this->_buildRequest($request, '_buildCancelRequest');
        $rawResponse = $this->_sendRequest($this->getConfiguration()->getApiUrl(), $rawRequest);
        $response    = $this->_parseResponse($rawResponse);
        $this->_stamp($response, __FUNCTION__);
        return $response;
    }

    /**
     * mask some critical information in transaction request.
     *
     * @param string $rawRequest
     *
     * @return string
     */
    protected function _maskRequest( $rawRequest )
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
        $data      = array(
            'transaction' => $backtrace[2]['function'],
            'request'     => $this->_maskRequest($this->getConnector()->getLastSentRequest()),
            'response'    => $this->getConnector()->getLastReceivedResponse(),
        );
        return $data;
    }
}
