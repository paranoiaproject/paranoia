<?php
namespace Paranoia\Payment\Adapter;

use \StdClass;
use Paranoia\Payment\Request;
use Paranoia\Payment\Response;
use Paranoia\Payment\TransferInterface;
use Paranoia\Payment\Exception\UnknownTransactionType;
use Paranoia\Payment\Exception\UnknownCurrencyCode;
use Paranoia\Payment\Exception\ConfigurationError;
use Paranoia\Communication\Connector;
use Paranoia\EventManager\EventManagerAbstract;

abstract class AdapterAbstract extends EventManagerAbstract
{

    const CONNECTOR_TYPE = Connector::CONNECTOR_TYPE_HTTP;
    /* Currency Types */
    const CURRENCY_TRY = 'TRY';
    const CURRENCY_USD = 'USD';
    const CURRENCY_EUR = 'EUR';
    /* Event Triggers */
    const EVENT_ON_TRANSACTION_SUCCESSFUL = 'OnTransactionSuccessful';
    const EVENT_ON_TRANSACTION_FAILED     = 'OnTransactionFailed';
    const EVENT_ON_EXCEPTION              = 'OnException';
    /* Transaction Types*/
    const TRANSACTION_TYPE_PREAUTHORIZATION  = 'preAuthorization';
    const TRANSACTION_TYPE_POSTAUTHORIZATION = 'postAuthorization';
    const TRANSACTION_TYPE_SALE              = 'sale';
    const TRANSACTION_TYPE_CANCEL            = 'cancel';
    const TRANSACTION_TYPE_REFUND            = 'refund';
    const TRANSACTION_TYPE_POINT_QUERY       = 'pointQuery';
    const TRANSACTION_TYPE_POINT_USAGE       = 'pointUsage';
    protected $transactionMap = array();
    /**
     * @var \StdClass
     */
    protected $config;
    /**
     * @var \Paranoia\Communication\Connector
     */
    protected $connector;

    public function __construct(StdClass $config)
    {
        $this->config    = $config;
        $this->connector = new Connector(static::CONNECTOR_TYPE);
    }

    /**
     * build request data for preauthorization transaction.
     *
     * @param \Paranoia\Payment\Request $request
     * @return mixed
     */
    abstract protected function buildPreAuthorizationRequest(Request $request);

    /**
     * build request data for postauthorization transaction.
     *
     * @param \Paranoia\Payment\Request $request
     * @return mixed
     */
    abstract protected function buildPostAuthorizationRequest(Request $request);

    /**
     * build request data for sale transaction.
     *
     * @param \Paranoia\Payment\Request $request
     * @return mixed
     */
    abstract protected function buildSaleRequest(Request $request);

    /**
     * build request data for refund transaction.
     *
     * @param \Paranoia\Payment\Request $request
     * @return mixed
     */
    abstract protected function buildRefundRequest(Request $request);

    /**
     * build request data for cancel transaction.
     *
     * @param \Paranoia\Payment\Request $request
     * @return mixed
     */
    abstract protected function buildCancelRequest(Request $request);

    /**
     *  build complete raw data for the specified request.
     *
     * @param \Paranoia\Payment\Request $request
     * @param string                    $requestBuilder
     * @return mixed
     */
    abstract protected function buildRequest(Request $request, $requestBuilder);

    /**
     * parses response from returned provider.
     *
     * @param string $rawResponse
     * @return \Paranoia\Payment\Response\PaymentResponse
     */
    abstract protected function parseResponse($rawResponse);

    /**
     * returns connector object.
     *
     * @return \Paranoia\Communication\Adapter\AdapterInterface
     */
    public function getConnector()
    {
        return $this->connector;
    }

    /**
     * sends request to remote host.
     *
     * @param string $url
     * @param mixed  $data
     * @param array  $options
     * @throws \ErrorException|\Exception
     * @return mixed
     */
    protected function sendRequest($url, $data, $options = null)
    {
        try {
            return $this->getConnector()->sendRequest($url, $data, $options);
        } catch ( \ErrorException $e ) {
            $this->triggerEvent(
                self::EVENT_ON_EXCEPTION,
                array(
                    'exception' => $e,
                    'request'   => $this->maskRequest($this->getConnector()->getLastSentRequest()),
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
     * @throws \Paranoia\Payment\Exception\ConfigurationError
     * @throws \Paranoia\Payment\Exception\UnknownCurrencyCode
     * @return integer
     */
    protected function formatCurrency($currency)
    {
        if (!property_exists($this->config, 'currencyCodes')) {
            throw new ConfigurationError('Currency codes are not defined in configuration.');
        }
        if (!property_exists($this->config->currencyCodes, $currency)) {
            throw new UnknownCurrencyCode(sprintf('%s is unknown currency.', $currency));
        }
        return $this->config->currencyCodes->{$currency};
    }

    /**
     * returns formatted amount with doth or without doth.
     * formatted number returns amount default without doth.
     *
     * @param string|float $amount
     * @param boolean      $reverse
     * @return string
     */
    protected function formatAmount($amount, $reverse = false)
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
     * @return string
     */
    protected function formatExpireDate($month, $year)
    {
        return sprintf('%02s/%04s', $month, $year);
    }

    /**
     * returns formatted installment amount
     *
     * @param int $installment
     * @return string
     */
    protected function formatInstallment($installment)
    {
        return (!is_numeric($installment) || intval($installment) <= 1) ? '' : $installment;
    }

    /**
     * stamps transfer objects with time and transaction type.
     *
     * @param \Paranoia\Payment\TransferInterface $transfer
     * @param string                              $transactionType
     */
    private function stamp(TransferInterface $transfer, $transactionType)
    {
        $transfer->setTime(microtime(true));
        $transfer->setTransactionType($transactionType);
    }

    /**
     * returns transaction code by expected provider.
     *
     * @param string $transactionType
     * @throws \Paranoia\Payment\Exception\UnknownTransactionType
     * @return string
     */
    protected function getProviderTransactionType($transactionType)
    {
        if (!array_key_exists($transactionType, $this->transactionMap)) {
            throw new UnknownTransactionType('Transaction type is unknown: ' . $transactionType);
        }
        return $this->transactionMap[$transactionType];
    }

    /**
     * @see \Paranoia\Payment\Adapter\AdapterInterface::preAuthorization()
     * @param \Paranoia\Payment\Request $request
     * @return object
     */
    public function preAuthorization(Request $request)
    {
        $this->stamp($request, __FUNCTION__);
        $rawRequest  = $this->buildRequest($request, 'buildPreAuthorizationRequest');
        $rawResponse = $this->sendRequest($this->config->api_url, $rawRequest);
        $response    = $this->parseResponse($rawResponse);
        $this->stamp($response, __FUNCTION__);
        return $response;
    }

    /**
     * @see \Paranoia\Payment\Adapter\AdapterInterface::postAuthorization()
     * @param \Paranoia\Payment\Request $request
     * @return object
     */
    public function postAuthorization(Request $request)
    {
        $this->stamp($request, __FUNCTION__);
        $rawRequest  = $this->buildRequest($request, 'buildPostAuthorizationRequest');
        $rawResponse = $this->sendRequest($this->config->api_url, $rawRequest);
        $response    = $this->parseResponse($rawResponse);
        $this->stamp($response, __FUNCTION__);
        return $response;
    }

    /**
     * @see \Paranoia\Payment\Adapter\AdapterInterface::sale()
     * @param \Paranoia\Payment\Request $request
     * @return object
     */
    public function sale(Request $request)
    {
        $this->stamp($request, __FUNCTION__);
        $rawRequest  = $this->buildRequest($request, 'buildSaleRequest');
        $rawResponse = $this->sendRequest($this->config->api_url, $rawRequest);
        $response    = $this->parseResponse($rawResponse);
        $this->stamp($response, __FUNCTION__);
        return $response;
    }

    /**
     * @see \Paranoia\Payment\Adapter\AdapterInterface::refund()
     * @param \Paranoia\Payment\Request $request
     * @return object
     */
    public function refund(Request $request)
    {
        $this->stamp($request, __FUNCTION__);
        $rawRequest  = $this->buildRequest($request, 'buildRefundRequest');
        $rawResponse = $this->sendRequest($this->config->api_url, $rawRequest);
        $response    = $this->parseResponse($rawResponse);
        $this->stamp($response, __FUNCTION__);
        return $response;
    }

    /**
     * @see \Paranoia\Payment\Adapter\AdapterInterface::cancel()
     * @param \Paranoia\Payment\Request $request
     * @return object
     */
    public function cancel(Request $request)
    {
        $this->stamp($request, __FUNCTION__);
        $rawRequest  = $this->buildRequest($request, 'buildCancelRequest');
        $rawResponse = $this->sendRequest($this->config->api_url, $rawRequest);
        $response    = $this->parseResponse($rawResponse);
        $this->stamp($response, __FUNCTION__);
        return $response;
    }

    /**
     * @see \Paranoia\Payment\Adapter\AdapterInterface::pointQuery()
     * @param \Paranoia\Payment\Request $request
     * @return object
     */
    public function pointQuery(Request $request)
    {
        $this->stamp($request, __FUNCTION__);
        $rawRequest  = $this->buildRequest($request, 'buildPointQueryRequest');
        $rawResponse = $this->sendRequest($this->config->api_url, $rawRequest);
        $response    = $this->parseResponse($rawResponse);
        $this->stamp($response, __FUNCTION__);
        return $response;
    }

    /**
     * @see \Paranoia\Payment\Adapter\AdapterInterface::pointUsage()
     * @param \Paranoia\Payment\Request $request
     * @return object
     */
    public function pointUsage(Request $request)
    {
        $this->stamp($request, __FUNCTION__);
        $rawRequest  = $this->buildRequest($request, 'buildPointUsageRequest');
        $rawResponse = $this->sendRequest($this->config->api_url, $rawRequest);
        $response    = $this->parseResponse($rawResponse);
        $this->stamp($response, __FUNCTION__);
        return $response;
    }

    /**
     * mask some critical information in transaction request.
     *
     * @param string $rawRequest
     * @return string
     */
    protected function maskRequest($rawRequest)
    {
        return $rawRequest;
    }

    /**
     * collects transaction information.
     *
     * @return array
     */
    protected function collectTransactionInformation()
    {
        $backtrace = debug_backtrace();
        $data      = array(
            'transaction' => $backtrace[2]['function'],
            'request'     => $this->maskRequest($this->getConnector()->getLastSentRequest()),
            'response'    => $this->getConnector()->getLastReceivedResponse(),
        );
        return $data;
    }
}
