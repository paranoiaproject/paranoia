<?php
namespace Paranoia\Nestpay\Transaction;


use GuzzleHttp\ClientInterface;
use Paranoia\Configuration\NestpayConfiguration;
use Psr\Http\Message\ResponseInterface;

abstract class BaseTransaction
{
    /** @var NestpayConfiguration */
    protected $configuration;

    /** @var ClientInterface */
    protected $client;

    /**
     * BaseTransaction constructor.
     * @param NestpayConfiguration $configuration
     * @param ClientInterface $client
     */
    public function __construct(NestpayConfiguration $configuration, ClientInterface $client)
    {
        $this->configuration = $configuration;
        $this->client = $client;
    }

    protected function sendRequest(array $data): ResponseInterface
    {
        
    }
}
