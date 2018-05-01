<?php
namespace Paranoia\Pos;

use Guzzle\Http\Client as HttpClient;
use Guzzle\Http\Exception\RequestException;
use Paranoia\Configuration\AbstractConfiguration;
use Paranoia\Exception\CommunicationError;
use Paranoia\Request;
use Paranoia\TransactionType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractPos
{
    /**
     * @var AbstractConfiguration
     */
    protected $configuration;

    /** @var EventDispatcherInterface  */
    private $dispatcher;

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

    private function performTransaction(Request $request, $transactionType)
    {
        $rawRequest  = $this->buildRequest($request, $transactionType);
        $rawResponse = $this->sendRequest($this->configuration->getApiUrl(), $rawRequest);
        $response    = $this->parseResponse($rawResponse, $transactionType);
        return $response;
    }

    /**
     * @param \Paranoia\Request $request
     *
     * @return \Paranoia\Response\PaymentResponse
     */
    public function preAuthorization(Request $request)
    {
        return $this->performTransaction($request, TransactionType::PRE_AUTHORIZATION);
    }

    /**
     * @param \Paranoia\Request $request
     *
     * @return \Paranoia\Response\PaymentResponse
     */
    public function postAuthorization(Request $request)
    {
        return $this->performTransaction($request, TransactionType::POST_AUTHORIZATION);
    }

    /**
     * @param \Paranoia\Request $request
     *
     * @return \Paranoia\Response\PaymentResponse
     */
    public function sale(Request $request)
    {
        return $this->performTransaction($request, TransactionType::SALE);
    }

    /**
     * @param \Paranoia\Request $request
     *
     * @return \Paranoia\Response\PaymentResponse
     */
    public function refund(Request $request)
    {
        return $this->performTransaction($request, TransactionType::REFUND);
    }

    /**
     * @param \Paranoia\Request $request
     *
     * @return \Paranoia\Response\PaymentResponse
     */
    public function cancel(Request $request)
    {
        return $this->performTransaction($request, TransactionType::CANCEL);
    }

    /**
     * @param \Paranoia\Request $request
     *
     * @return \Paranoia\Response\PaymentResponse
     */
    public function pointQuery(Request $request)
    {
        return $this->performTransaction($request, TransactionType::POINT_INQUIRY);
    }

    /**
     * @param \Paranoia\Request $request
     *
     * @return \Paranoia\Response\PaymentResponse
     */
    public function pointUsage(Request $request)
    {
        return $this->performTransaction($request, TransactionType::POINT_USAGE);
    }
}
