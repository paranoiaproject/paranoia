<?php
namespace Paranoia\Acquirer\Gvp\Service;

use Paranoia\Acquirer\Gvp\RequestBuilder\RefundRequestBuilder;
use Paranoia\Acquirer\Gvp\ResponseParser\RefundResponseParser;
use Paranoia\Core\Acquirer\Service\RefundService;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\CommunicationError;
use Paranoia\Core\Model\Request\RefundRequest;
use Paranoia\Core\Model\Response\RefundResponse;
use Paranoia\Lib\HttpClient;

/**
 * Class RefundServiceImp
 * @package Paranoia\Acquirer\Gvp\Service
 */
class RefundServiceImp implements RefundService
{
    /** @var RefundRequestBuilder */
    private $requestBuilder;

    /** @var RefundResponseParser */
    private $responseParser;

    /** @var HttpClient */
    private $httpClient;

    /**
     * RefundServiceImp constructor.
     * @param RefundRequestBuilder $requestBuilder
     * @param RefundResponseParser $responseParser
     * @param HttpClient $httpClient
     */
    public function __construct(RefundRequestBuilder $requestBuilder, RefundResponseParser $responseParser, HttpClient $httpClient)
    {
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
        $this->httpClient = $httpClient;
    }

    /**
     * @param RefundRequest $request
     * @return RefundResponse
     * @throws BadResponseException
     * @throws CommunicationError
     */
    public function process(RefundRequest $request): RefundResponse
    {
        $providerRequest = $this->requestBuilder->build($request);
        $rawResponse = $this->httpClient->send($providerRequest);
        return $this->responseParser->parse($rawResponse);
    }
}