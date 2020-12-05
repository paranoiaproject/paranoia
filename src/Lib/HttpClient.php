<?php
namespace Paranoia\Lib;

use Guzzle\Http\Client;
use Paranoia\Core\Exception\CommunicationError;
use Paranoia\Core\Model\Request\HttpRequest;

/**
 * Class HttpClient
 * @package Paranoia\Lib
 */
class HttpClient
{
    /** @var Client */
    private $client;

    /**
     * HttpClient constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param HttpRequest $request
     * @return string
     * @throws CommunicationError
     */
    public function send(HttpRequest $request): string
    {
        $httpRequest = $this->client->createRequest(
            $request->getMethod(),
            $request->getUrl(),
            $request->getHeaders(),
            $request->getBody()
        );

        try {
            return $this->client->send($httpRequest)->getBody();
        } catch (\Exception $exception) {
            throw new CommunicationError($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}