<?php
namespace Paranoia\Nestpay\Transaction;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Paranoia\Configuration\NestpayConfiguration;
use Paranoia\Core\Exception\BadResponseException;
use Psr\Http\Message\ResponseInterface;

abstract class BaseTransaction
{
    /** @var NestpayConfiguration */
    protected $configuration;

    /** @var Client */
    protected $client;

    /**
     * BaseTransaction constructor.
     * @param NestpayConfiguration $configuration
     * @param Client $client
     */
    public function __construct(NestpayConfiguration $configuration, Client $client)
    {
        $this->configuration = $configuration;
        $this->client = $client;
    }

    /**
     * @param array $data
     * @return ResponseInterface
     * @throws BadResponseException
     */
    protected function sendRequest(array $data): ResponseInterface
    {
        try {
            /** @var ResponseInterface $response */
            return $this->client->post($this->configuration->getApiUrl(), [
                'verify' => true,
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2
                ],
                'form_params' => $data,
            ]);
        } catch (GuzzleException $exception) {
            throw new BadResponseException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
