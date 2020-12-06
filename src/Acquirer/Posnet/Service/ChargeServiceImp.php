<?php
namespace Paranoia\Acquirer\Posnet\Service;

use Paranoia\Acquirer\Posnet\RequestBuilder\ChargeRequestBuilder;
use Paranoia\Acquirer\Posnet\ResponseParser\ChargeResponseParser;
use Paranoia\Core\Acquirer\Service\ChargeService;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\CommunicationError;
use Paranoia\Core\Model\Request\ChargeRequest;
use Paranoia\Core\Model\Response\ChargeResponse;
use Paranoia\Lib\HttpClient;

/**
 * Class ChargeServiceImp
 * @package Paranoia\Acquirer\Posnet\Service
 */
class ChargeServiceImp implements ChargeService
{
    /** @var ChargeRequestBuilder */
    private $requestBuilder;

    /** @var ChargeResponseParser */
    private $responseParser;

    /** @var HttpClient */
    private $httpClient;

    /**
     * ChargeServiceImp constructor.
     * @param ChargeRequestBuilder $requestBuilder
     * @param ChargeResponseParser $responseParser
     * @param HttpClient $httpClient
     */
    public function __construct(
        ChargeRequestBuilder $requestBuilder,
        ChargeResponseParser $responseParser,
        HttpClient $httpClient
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
        $this->httpClient = $httpClient;
    }

    /**
     * @param ChargeRequest $request
     * @return ChargeResponse
     * @throws BadResponseException
     * @throws CommunicationError
     */
    public function process(ChargeRequest $request): ChargeResponse
    {
        $providerRequest = $this->requestBuilder->build($request);
        $rawResponse = $this->httpClient->send($providerRequest);
        return $this->responseParser->parse($rawResponse);
    }
}