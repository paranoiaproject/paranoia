<?php
namespace Paranoia\Payment\Adapter;

use Guzzle\Http\Client as HttpClient;
use Paranoia\Configuration\AbstractConfiguration;
use Paranoia\Exception\InvalidArgumentException;
use Paranoia\Exception\CommunicationError;
use Paranoia\Payment\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Guzzle\Http\Exception\RequestException;

abstract class AdapterAbstract
{
    /* Events */
    const EVENT_ON_TRANSACTION_SUCCESSFUL = 'OnTransactionSuccessful';
    const EVENT_ON_TRANSACTION_FAILED = 'OnTransactionFailed';
    const EVENT_ON_EXCEPTION = 'OnException';

    /* Transaction Types*/
    const TRANSACTION_TYPE_PREAUTHORIZATION = 'preAuthorization';
    const TRANSACTION_TYPE_POSTAUTHORIZATION = 'postAuthorization';
    const TRANSACTION_TYPE_SALE = 'sale';
    const TRANSACTION_TYPE_CANCEL = 'cancel';
    const TRANSACTION_TYPE_REFUND = 'refund';
    const TRANSACTION_TYPE_POINT_QUERY = 'pointQuery';
    const TRANSACTION_TYPE_POINT_USAGE = 'pointUsage';

    /**
     * @var array
     */
    protected $transactionMap = array();

    /**
     * @var AbstractConfiguration
     */
    protected $configuration;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $dispatcher;


    public function __construct(AbstractConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param \Paranoia\Configuration\AbstractConfiguration $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return EventDispatcher
     */
    protected function getDispatcher()
    {
        if (!$this->dispatcher) {
            $this->dispatcher = new EventDispatcher();
        }
        return $this->dispatcher;
    }

    /**
     * build request data for preauthorization transaction.
     *
     * @param \Paranoia\Payment\Request $request
     *
     * @return mixed
     */
    abstract protected function buildPreAuthorizationRequest(Request $request);

    /**
     * build request data for postauthorization transaction.
     *
     * @param \Paranoia\Payment\Request $request
     *
     * @return mixed
     */
    abstract protected function buildPostAuthorizationRequest(Request $request);

    /**
     * build request data for sale transaction.
     *
     * @param \Paranoia\Payment\Request $request
     *
     * @return mixed
     */
    abstract protected function buildSaleRequest(Request $request);

    /**
     * build request data for refund transaction.
     *
     * @param \Paranoia\Payment\Request $request
     *
     * @return mixed
     */
    abstract protected function buildRefundRequest(Request $request);

    /**
     * build request data for cancel transaction.
     *
     * @param \Paranoia\Payment\Request $request
     *
     * @return mixed
     */
    abstract protected function buildCancelRequest(Request $request);

    /**
     *  build complete raw data for the specified request.
     *
     * @param \Paranoia\Payment\Request $request
     * @param string                    $requestBuilder
     *
     * @return mixed
     */
    abstract protected function buildRequest(Request $request, $requestBuilder);

    /**
     * parses response from returned provider.
     *
     * @param string $rawResponse
     * @param string $transactionType
     *
     * @return \Paranoia\Payment\Response\PaymentResponse
     */
    abstract protected function parseResponse($rawResponse, $transactionType);

    /**
     * Makes http request to remote host.
     *
     * @param string $url
     * @param mixed  $data
     * @param array $options
     *
     * @throws \ErrorException|\Exception
     * @return mixed
     */
    protected function sendRequest($url, $data, $options = null)
    {
        $client = new HttpClient();
        $client->setConfig(array(
           'curl.options' => array(
               CURLOPT_SSL_VERIFYPEER => false,
               CURLOPT_SSL_VERIFYHOST => false,
           )
        ));
        $request = $client->post($url, null, $data);
        try {
            return $request->send()->getBody();
        } catch (RequestException $e) {
            throw new CommunicationError('Communication failed: ' . $url);
        }
    }

    /**
     * returns transaction code by expected provider.
     *
     * @param string $transactionType
     *
     * @throws \Paranoia\Exception\InvalidArgumentException
     * @return string
     */
    protected function getProviderTransactionType($transactionType)
    {
        if (!array_key_exists($transactionType, $this->transactionMap)) {
            throw new InvalidArgumentException('Transaction type is unknown: ' . $transactionType);
        }
        return $this->transactionMap[$transactionType];
    }

    /**
     * @param \Paranoia\Payment\Request $request
     *
     * @return \Paranoia\Payment\Response\PaymentResponse
     */
    public function preAuthorization(Request $request)
    {
        $rawRequest  = $this->buildRequest($request, 'buildPreauthorizationRequest');
        $rawResponse = $this->sendRequest($this->configuration->getApiUrl(), $rawRequest);
        $response    = $this->parseResponse($rawResponse, self::TRANSACTION_TYPE_PREAUTHORIZATION);
        return $response;
    }

    /**
     * @param \Paranoia\Payment\Request $request
     *
     * @return \Paranoia\Payment\Response\PaymentResponse
     */
    public function postAuthorization(Request $request)
    {
        $rawRequest  = $this->buildRequest($request, 'buildPostAuthorizationRequest');
        $rawResponse = $this->sendRequest($this->configuration->getApiUrl(), $rawRequest);
        $response    = $this->parseResponse($rawResponse, self::TRANSACTION_TYPE_POSTAUTHORIZATION);
        return $response;
    }

    /**
     * @param \Paranoia\Payment\Request $request
     *
     * @return \Paranoia\Payment\Response\PaymentResponse
     */
    public function sale(Request $request)
    {
        $rawRequest  = $this->buildRequest($request, 'buildSaleRequest');
        $rawResponse = $this->sendRequest($this->configuration->getApiUrl(), $rawRequest);
        $response    = $this->parseResponse($rawResponse, self::TRANSACTION_TYPE_SALE);
        return $response;
    }

    /**
     * @param \Paranoia\Payment\Request $request
     *
     * @return \Paranoia\Payment\Response\PaymentResponse
     */
    public function refund(Request $request)
    {
        $rawRequest  = $this->buildRequest($request, 'buildRefundRequest');
        $rawResponse = $this->sendRequest($this->configuration->getApiUrl(), $rawRequest);
        $response    = $this->parseResponse($rawResponse, self::TRANSACTION_TYPE_REFUND);
        return $response;
    }

    /**
     * @param \Paranoia\Payment\Request $request
     *
     * @return \Paranoia\Payment\Response\PaymentResponse
     */
    public function cancel(Request $request)
    {
        $rawRequest  = $this->buildRequest($request, 'buildCancelRequest');
        $rawResponse = $this->sendRequest($this->configuration->getApiUrl(), $rawRequest);
        $response    = $this->parseResponse($rawResponse, self::TRANSACTION_TYPE_CANCEL);
        return $response;
    }

    /**
     * @param \Paranoia\Payment\Request $request
     *
     * @return \Paranoia\Payment\Response\PaymentResponse
     */
    public function pointQuery(Request $request)
    {
        $rawRequest  = $this->buildRequest($request, 'buildPointQueryRequest');
        $rawResponse = $this->sendRequest($this->configuration->getApiUrl(), $rawRequest);
        $response    = $this->parseResponse($rawResponse, self::TRANSACTION_TYPE_POINT_QUERY);
        return $response;
    }

    /**
     * @param \Paranoia\Payment\Request $request
     *
     * @return \Paranoia\Payment\Response\PaymentResponse
     */
    public function pointUsage(Request $request)
    {
        $rawRequest  = $this->buildRequest($request, 'buildPointUsageRequest');
        $rawResponse = $this->sendRequest($this->configuration->getApiUrl(), $rawRequest);
        $response    = $this->parseResponse($rawResponse, self::TRANSACTION_TYPE_POINT_USAGE);
        return $response;
    }

    /**
     * mask some critical information in transaction request.
     *
     * @param string $rawRequest
     *
     * @return string
     */
    protected function maskRequest($rawRequest)
    {
        return $rawRequest;
    }
}
