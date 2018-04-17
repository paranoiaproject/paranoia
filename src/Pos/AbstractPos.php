<?php
namespace Paranoia\Pos;

use Guzzle\Http\Client as HttpClient;
use Guzzle\Http\Exception\RequestException;
use Paranoia\Configuration\AbstractConfiguration;
use Paranoia\Exception\CommunicationError;
use Paranoia\Request;
use Paranoia\TransactionType;

abstract class AbstractPos
{
    /**
     * @var AbstractConfiguration
     */
    protected $configuration;


    public function __construct(AbstractConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     *  build complete raw data for the specified request.
     *
     * @param \Paranoia\Request $request
     * @param string $transactionType
     *
     * @return mixed
     */
    abstract protected function buildRequest(Request $request, $transactionType);

    /**
     * parses response from returned provider.
     *
     * @param string $rawResponse
     * @param string $transactionType
     *
     * @return \Paranoia\Response\PaymentResponse
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
     * @param \Paranoia\Request $request
     *
     * @return \Paranoia\Response\PaymentResponse
     */
    public function preAuthorization(Request $request)
    {
        $rawRequest  = $this->buildRequest($request, TransactionType::PRE_AUTHORIZATION);
        $rawResponse = $this->sendRequest($this->configuration->getApiUrl(), $rawRequest);
        $response    = $this->parseResponse($rawResponse, TransactionType::PRE_AUTHORIZATION);
        return $response;
    }

    /**
     * @param \Paranoia\Request $request
     *
     * @return \Paranoia\Response\PaymentResponse
     */
    public function postAuthorization(Request $request)
    {
        $rawRequest  = $this->buildRequest($request, TransactionType::POST_AUTHORIZATION);
        $rawResponse = $this->sendRequest($this->configuration->getApiUrl(), $rawRequest);
        $response    = $this->parseResponse($rawResponse, TransactionType::POST_AUTHORIZATION);
        return $response;
    }

    /**
     * @param \Paranoia\Request $request
     *
     * @return \Paranoia\Response\PaymentResponse
     */
    public function sale(Request $request)
    {
        $rawRequest  = $this->buildRequest($request, TransactionType::SALE);
        $rawResponse = $this->sendRequest($this->configuration->getApiUrl(), $rawRequest);
        $response    = $this->parseResponse($rawResponse, TransactionType::SALE);
        return $response;
    }

    /**
     * @param \Paranoia\Request $request
     *
     * @return \Paranoia\Response\PaymentResponse
     */
    public function refund(Request $request)
    {
        $rawRequest  = $this->buildRequest($request, TransactionType::REFUND);
        $rawResponse = $this->sendRequest($this->configuration->getApiUrl(), $rawRequest);
        $response    = $this->parseResponse($rawResponse, TransactionType::REFUND);
        return $response;
    }

    /**
     * @param \Paranoia\Request $request
     *
     * @return \Paranoia\Response\PaymentResponse
     */
    public function cancel(Request $request)
    {
        $rawRequest  = $this->buildRequest($request, TransactionType::CANCEL);
        $rawResponse = $this->sendRequest($this->configuration->getApiUrl(), $rawRequest);
        $response    = $this->parseResponse($rawResponse, TransactionType::CANCEL);
        return $response;
    }

    /**
     * @param \Paranoia\Request $request
     *
     * @return \Paranoia\Response\PaymentResponse
     */
    public function pointQuery(Request $request)
    {
        $rawRequest  = $this->buildRequest($request, TransactionType::POINT_INQUIRY);
        $rawResponse = $this->sendRequest($this->configuration->getApiUrl(), $rawRequest);
        $response    = $this->parseResponse($rawResponse, TransactionType::POINT_INQUIRY);
        return $response;
    }

    /**
     * @param \Paranoia\Request $request
     *
     * @return \Paranoia\Response\PaymentResponse
     */
    public function pointUsage(Request $request)
    {
        $rawRequest  = $this->buildRequest($request, TransactionType::POINT_USAGE);
        $rawResponse = $this->sendRequest($this->configuration->getApiUrl(), $rawRequest);
        $response    = $this->parseResponse($rawResponse, TransactionType::POINT_USAGE);
        return $response;
    }
}
