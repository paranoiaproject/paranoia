<?php
namespace Paranoia\Acquirer\Gvp\Service;

use Paranoia\Acquirer\Gvp\RequestBuilder\AuthorizationRequestBuilder;
use Paranoia\Acquirer\Gvp\ResponseParser\AuthorizationResponseParser;
use Paranoia\Core\Acquirer\Service\AuthorizationService;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\CommunicationError;
use Paranoia\Core\Model\Request\AuthorizationRequest;
use Paranoia\Core\Model\Response\AuthorizationResponse;
use Paranoia\Lib\HttpClient;

/**
 * Class AuthorizationServiceImp
 * @package Paranoia\Acquirer\Gvp\Service
 */
class AuthorizationServiceImp implements AuthorizationService
{
    /** @var AuthorizationRequestBuilder */
    private $requestBuilder;

    /** @var AuthorizationResponseParser */
    private $responseParser;

    /** @var HttpClient */
    private $httpClient;

    /**
     * AuthorizationServiceImp constructor.
     * @param AuthorizationRequestBuilder $requestBuilder
     * @param AuthorizationResponseParser $responseParser
     * @param HttpClient $httpClient
     */
    public function __construct(
        AuthorizationRequestBuilder $requestBuilder,
        AuthorizationResponseParser $responseParser,
        HttpClient $httpClient
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
        $this->httpClient = $httpClient;
    }

    /**
     * @param AuthorizationRequest $request
     * @return AuthorizationResponse
     * @throws BadResponseException
     * @throws CommunicationError
     */
    public function process(AuthorizationRequest $request): AuthorizationResponse
    {
        $providerRequest = $this->requestBuilder->build($request);
        $rawResponse = $this->httpClient->send($providerRequest);
        return $this->responseParser->parse($rawResponse);
    }
}