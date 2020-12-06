<?php
namespace Paranoia\Acquirer\Gvp\Service;

use Paranoia\Acquirer\Gvp\RequestBuilder\CancelRequestBuilder;
use Paranoia\Acquirer\Gvp\ResponseParser\CancelResponseParser;
use Paranoia\Core\Acquirer\Service\CancelService;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\CommunicationError;
use Paranoia\Core\Model\Request\CancelRequest;
use Paranoia\Core\Model\Response\CancelResponse;
use Paranoia\Lib\HttpClient;

/**
 * Class CancelServiceImp
 * @package Paranoia\Acquirer\Gvp\Service
 */
class CancelServiceImp implements CancelService
{
    /** @var CancelRequestBuilder */
    private $requestBuilder;

    /** @var CancelResponseParser */
    private $responseParser;

    /** @var HttpClient */
    private $httpClient;

    /**
     * CancelServiceImp constructor.
     * @param CancelRequestBuilder $requestBuilder
     * @param CancelResponseParser $responseParser
     * @param HttpClient $httpClient
     */
    public function __construct(
        CancelRequestBuilder $requestBuilder,
        CancelResponseParser $responseParser,
        HttpClient $httpClient
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
        $this->httpClient = $httpClient;
    }

    /**
     * @param CancelRequest $request
     * @return CancelResponse
     * @throws BadResponseException
     * @throws CommunicationError
     */
    public function process(CancelRequest $request): CancelResponse
    {
        $providerRequest = $this->requestBuilder->build($request);
        $rawResponse = $this->httpClient->send($providerRequest);
        return $this->responseParser->parse($rawResponse);
    }
}