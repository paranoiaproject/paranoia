<?php
namespace Paranoia\Pos;

use Guzzle\Http\Client as HttpClient;
use Guzzle\Http\Exception\RequestException;
use Paranoia\Configuration\AbstractConfiguration;
use Paranoia\Exception\CommunicationError;
use Paranoia\Request\Request;
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
     * @param \Paranoia\Request\Request $request
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
     * @return \Paranoia\Response
     */
    abstract protected function parseResponse($rawResponse, $transactionType);

    /**
     * Makes http request to remote host.
     *
     * @param string $url
     * @param mixed  $data
     * @param array $options
     *
     * @throws CommunicationError
     * @return mixed
     */
    protected function sendRequest($url, $data, $options = null)
    {
        $client = new HttpClient();

        $config =  array(
            'curl.options' => array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            )
        );

        //override default config
        if(is_array($this->configuration->getGuzzleConfig())){
            $config = array_replace_recursive($config, $this->configuration->getGuzzleConfig());
        }

        $client->setConfig($config);
        $request = $client->post($url, null, $data);
        try {
            return $request->send()->getBody();
        } catch (RequestException $e) {
            throw new CommunicationError('Communication failed: ' . $url);
        }
    }

    /**
     * @param $request Request
     * @param $transactionType
     * @return \Paranoia\Response
     * @throws CommunicationError
    */
    private function performTransaction(Request $request, $transactionType)
    {
        $rawRequest  = $this->buildRequest($request, $transactionType);
        $rawResponse = $this->sendRequest($this->configuration->getApiUrl(), $rawRequest);
        $response    = $this->parseResponse($rawResponse, $transactionType);
        return $response;
    }

    /**
     * @param \Paranoia\Request\Request $request
     *
     * @return \Paranoia\Response
     * @throws CommunicationError
     */
    public function preAuthorization(Request $request)
    {
        return $this->performTransaction($request, TransactionType::PRE_AUTHORIZATION);
    }

    /**
     * @param \Paranoia\Request\Request $request
     *
     * @return \Paranoia\Response
     * @throws CommunicationError
     */
    public function postAuthorization(Request $request)
    {
        return $this->performTransaction($request, TransactionType::POST_AUTHORIZATION);
    }

    /**
     * @param \Paranoia\Request\Request $request
     *
     * @return \Paranoia\Response
     * @throws CommunicationError
     */
    public function sale(Request $request)
    {
        return $this->performTransaction($request, TransactionType::SALE);
    }

    /**
     * @param \Paranoia\Request\Request $request
     *
     * @return \Paranoia\Response
     * @throws CommunicationError
     */
    public function refund(Request $request)
    {
        return $this->performTransaction($request, TransactionType::REFUND);
    }

    /**
     * @param \Paranoia\Request\Request $request
     *
     * @return \Paranoia\Response
     * @throws CommunicationError
     */
    public function cancel(Request $request)
    {
        return $this->performTransaction($request, TransactionType::CANCEL);
    }

    /**
     * @param \Paranoia\Request\Request $request
     *
     * @return \Paranoia\Response
     * @throws CommunicationError
     */
    public function pointQuery(Request $request)
    {
        return $this->performTransaction($request, TransactionType::POINT_INQUIRY);
    }

    /**
     * @param \Paranoia\Request\Request $request
     *
     * @return \Paranoia\Response
     * @throws CommunicationError
     */
    public function pointUsage(Request $request)
    {
        return $this->performTransaction($request, TransactionType::POINT_USAGE);
    }
}
