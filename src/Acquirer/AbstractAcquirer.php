<?php
namespace Paranoia\Acquirer;

use Guzzle\Http\Client as HttpClient;
use Guzzle\Http\Exception\RequestException;
use Paranoia\Core\AbstractConfiguration;
use Paranoia\Core\Exception\CommunicationError;
use Paranoia\Core\Model\Request;
use Paranoia\Core\Constant\TransactionType;

abstract class AbstractAcquirer
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
     * @param \Paranoia\Core\Model\Request $request
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
     * @return \Paranoia\Core\Model\Response
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
        $client->setConfig(array(
            'curl.options' => array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2
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
     * @param $request Request
     * @param $transactionType
     * @return \Paranoia\Core\Model\Response
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
     * @param \Paranoia\Core\Model\Request $request
     *
     * @return \Paranoia\Core\Model\Response
     * @throws CommunicationError
     */
    public function preAuthorization(Request $request)
    {
        return $this->performTransaction($request, TransactionType::PRE_AUTHORIZATION);
    }

    /**
     * @param \Paranoia\Core\Model\Request $request
     *
     * @return \Paranoia\Core\Model\Response
     * @throws CommunicationError
     */
    public function postAuthorization(Request $request)
    {
        return $this->performTransaction($request, TransactionType::POST_AUTHORIZATION);
    }

    /**
     * @param \Paranoia\Core\Model\Request $request
     *
     * @return \Paranoia\Core\Model\Response
     * @throws CommunicationError
     */
    public function sale(Request $request)
    {
        return $this->performTransaction($request, TransactionType::SALE);
    }

    /**
     * @param \Paranoia\Core\Model\Request $request
     *
     * @return \Paranoia\Core\Model\Response
     * @throws CommunicationError
     */
    public function refund(Request $request)
    {
        return $this->performTransaction($request, TransactionType::REFUND);
    }

    /**
     * @param \Paranoia\Core\Model\Request $request
     *
     * @return \Paranoia\Core\Model\Response
     * @throws CommunicationError
     */
    public function cancel(Request $request)
    {
        return $this->performTransaction($request, TransactionType::CANCEL);
    }

    /**
     * @param \Paranoia\Core\Model\Request $request
     *
     * @return \Paranoia\Core\Model\Response
     * @throws CommunicationError
     */
    public function pointQuery(Request $request)
    {
        return $this->performTransaction($request, TransactionType::POINT_INQUIRY);
    }

    /**
     * @param \Paranoia\Core\Model\Request $request
     *
     * @return \Paranoia\Core\Model\Response
     * @throws CommunicationError
     */
    public function pointUsage(Request $request)
    {
        return $this->performTransaction($request, TransactionType::POINT_USAGE);
    }
}
