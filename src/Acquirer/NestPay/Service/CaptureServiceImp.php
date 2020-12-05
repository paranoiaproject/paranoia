<?php
namespace Paranoia\Acquirer\NestPay\Service;

use Paranoia\Acquirer\NestPay\RequestBuilder\CaptureRequestBuilder;
use Paranoia\Acquirer\NestPay\ResponseParser\CaptureResponseParser;
use Paranoia\Core\Acquirer\Service\CaptureService;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\CommunicationError;
use Paranoia\Core\Model\Request\CaptureRequest;
use Paranoia\Core\Model\Response\CaptureResponse;
use Paranoia\Lib\HttpClient;

/**
 * Class CaptureServiceImp
 * @package Paranoia\Acquirer\NestPay\Service
 */
class CaptureServiceImp implements CaptureService
{
    /** @var CaptureRequestBuilder */
    private $requestBuilder;

    /** @var CaptureResponseParser */
    private $responseParser;

    /** @var HttpClient */
    private $httpClient;

    /**
     * CaptureServiceImp constructor.
     * @param CaptureRequestBuilder $requestBuilder
     * @param CaptureResponseParser $responseParser
     * @param HttpClient $httpClient
     */
    public function __construct(
        CaptureRequestBuilder $requestBuilder,
        CaptureResponseParser $responseParser,
        HttpClient $httpClient
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
        $this->httpClient = $httpClient;
    }

    /**
     * @param CaptureRequest $request
     * @return CaptureResponse
     * @throws BadResponseException
     * @throws CommunicationError
     */
    public function process(CaptureRequest $request): CaptureResponse
    {
        $providerRequest = $this->requestBuilder->build($request);
        $rawResponse = $this->httpClient->send($providerRequest);
        return $this->responseParser->parse($rawResponse);
    }
}