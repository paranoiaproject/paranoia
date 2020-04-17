<?php
namespace Paranoia\Nestpay\Transaction;

use Paranoia\Configuration\NestpayConfiguration;
use Paranoia\Core\Request\CaptureRequest;
use Paranoia\Core\Response\CaptureResponse;
use Paranoia\Nestpay\RequestBuilder\CaptureRequestBuilder;
use Paranoia\Nestpay\ResponseParser\CaptureResponseParser;

class CaptureTransaction
{
    /** @var NestpayConfiguration */
    protected $configuration;

    /** @var CaptureRequestBuilder */
    private $requestBuilder;

    /** @var CaptureResponseParser */
    private $responseParser;

    /**
     * CaptureTransaction constructor.
     * @param NestpayConfiguration $configuration
     * @param CaptureRequestBuilder $requestBuilder
     * @param CaptureResponseParser $responseParser
     */
    public function __construct(NestpayConfiguration $configuration, CaptureRequestBuilder $requestBuilder, CaptureResponseParser $responseParser)
    {
        $this->configuration = $configuration;
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
    }

    public function perform(CaptureRequest $request): CaptureResponse
    {

    }
}
