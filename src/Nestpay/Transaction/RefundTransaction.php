<?php
namespace Paranoia\Nestpay\Transaction;

use GuzzleHttp\Client;
use Paranoia\Configuration\NestpayConfiguration;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\UnapprovedTransactionException;
use Paranoia\Core\Request\RefundRequest;
use Paranoia\Core\Response\RefundResponse;
use Paranoia\Nestpay\RequestBuilder\RefundRequestBuilder;
use Paranoia\Nestpay\ResponseParser\RefundResponseParser;

class RefundTransaction extends BaseTransaction
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
     * @param Client $client
     * @param RefundRequestBuilder $requestBuilder
     * @param RefundResponseParser $responseParser
     */
    public function __construct(NestpayConfiguration $configuration, Client $client, RefundRequestBuilder $requestBuilder, RefundResponseParser $responseParser)
    {
        parent::__construct($configuration, $client);
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
    }

    /**
     * @param RefundRequest $request
     * @return RefundResponse
     * @throws BadResponseException
     * @throws UnapprovedTransactionException
     */
    public function perform(RefundRequest $request): RefundResponse
    {
        $providerRequest = $this->requestBuilder->build($request);
        $providerResponse = $this->sendRequest($providerRequest);
        return  $this->responseParser->parse($providerResponse);
    }
}
