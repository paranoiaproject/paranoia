<?php
namespace Paranoia\Nestpay\Transaction;

use Paranoia\Configuration\NestpayConfiguration;
use Paranoia\Core\Request\ChargeRequest;
use Paranoia\Core\Response\ChargeResponse;
use Paranoia\Nestpay\RequestBuilder\ChargeRequestBuilder;
use Paranoia\Nestpay\ResponseParser\ChargeResponseParser;

class ChargeTransaction
{
    /** @var NestpayConfiguration */
    protected $configuration;

    /** @var ChargeRequestBuilder */
    private $requestBuilder;

    /** @var ChargeResponseParser */
    private $responseParser;

    /**
     * ChargeTransaction constructor.
     * @param NestpayConfiguration $configuration
     * @param ChargeRequestBuilder $requestBuilder
     * @param ChargeResponseParser $responseParser
     */
    public function __construct(NestpayConfiguration $configuration, ChargeRequestBuilder $requestBuilder, ChargeResponseParser $responseParser)
    {
        $this->configuration = $configuration;
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
    }

    public function perform(ChargeRequest $request): ChargeResponse
    {

    }
}
