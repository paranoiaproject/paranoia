<?php
namespace Paranoia\Nestpay\Transaction;

use Paranoia\Configuration\NestpayConfiguration;
use Paranoia\Core\Request\RefundRequest;
use Paranoia\Core\Response\RefundResponse;
use Paranoia\Nestpay\RequestBuilder\RefundRequestBuilder;
use Paranoia\Nestpay\ResponseParser\RefundResponseParser;

class RefundTransaction
{
    /** @var NestpayConfiguration */
    protected $configuration;

    /** @var RefundRequestBuilder */
    private $requestBuilder;

    /** @var RefundResponseParser */
    private $responseParser;

    /**
     * RefundTransaction constructor.
     * @param NestpayConfiguration $configuration
     * @param RefundRequestBuilder $requestBuilder
     * @param RefundResponseParser $responseParser
     */
    public function __construct(NestpayConfiguration $configuration, RefundRequestBuilder $requestBuilder, RefundResponseParser $responseParser)
    {
        $this->configuration = $configuration;
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
    }

    public function perform(RefundRequest $request): RefundResponse
    {

    }
}
