<?php
namespace Paranoia\Nestpay\Transaction;

use Paranoia\Configuration\NestpayConfiguration;
use Paranoia\Core\Request\CancelRequest;
use Paranoia\Core\Response\CancelResponse;
use Paranoia\Nestpay\RequestBuilder\CancelRequestBuilder;
use Paranoia\Nestpay\ResponseParser\CancelResponseParser;

class CancelTransaction
{
    /** @var NestpayConfiguration */
    protected $configuration;

    /** @var CancelRequestBuilder */
    private $requestBuilder;

    /** @var CancelResponseParser */
    private $responseParser;

    /**
     * CancelTransaction constructor.
     * @param NestpayConfiguration $configuration
     * @param CancelRequestBuilder $requestBuilder
     * @param CancelResponseParser $responseParser
     */
    public function __construct(NestpayConfiguration $configuration, CancelRequestBuilder $requestBuilder, CancelResponseParser $responseParser)
    {
        $this->configuration = $configuration;
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
    }

    public function perform(CancelRequest $request): CancelResponse
    {

    }
}
