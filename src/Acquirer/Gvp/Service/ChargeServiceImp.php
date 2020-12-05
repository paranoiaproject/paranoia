<?php
namespace Paranoia\Acquirer\Gvp\Service;

use Paranoia\Acquirer\Gvp\RequestBuilder\ChargeRequestBuilder;
use Paranoia\Acquirer\Gvp\ResponseParser\ChargeResponseParser;
use Paranoia\Core\Acquirer\Service\ChargeService;
use Paranoia\Core\Model\Request\ChargeRequest;
use Paranoia\Core\Model\Response\ChargeResponse;
use Paranoia\Lib\HttpClient;

/**
 * Class ChargeService
 * @package Paranoia\Acquirer\Gvp\Service
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
     * ChargeService constructor.
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
     * @inheritDoc
     */
    public function process(ChargeRequest $request): ChargeResponse
    {
        $providerRequest = $this->requestBuilder->build($request);
        $rawResponse = $this->httpClient->send($providerRequest);
        return $this->responseParser->parse($rawResponse);
    }
}