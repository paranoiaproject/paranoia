<?php
namespace Paranoia\Posnet\Transaction;

use GuzzleHttp\Client;
use Paranoia\Configuration\PosnetConfiguration;
use Paranoia\Core\Request\RefundRequest;
use Paranoia\Core\Response\RefundResponse;
use Paranoia\Core\Transaction\RefundTransaction as CoreRefundTransactionAlias;
use Paranoia\Posnet\RequestBuilder\RefundRequestBuilder;
use Paranoia\Posnet\ResponseParser\RefundResponseParser;

class RefundTransaction extends BaseTransaction implements CoreRefundTransactionAlias
{
    /** @var RefundRequestBuilder */
    protected $requestBuilder;

    /** @var RefundResponseParser */
    protected $responseParser;

    /**
     * RefundTransaction constructor.
     * @param PosnetConfiguration $configuration
     * @param Client $client
     * @param RefundRequestBuilder $requestBuilder
     * @param RefundResponseParser $responseParser
     */
    public function __construct(PosnetConfiguration $configuration, Client $client, RefundRequestBuilder $requestBuilder, RefundResponseParser $responseParser)
    {
        parent::__construct($configuration, $client);
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
    }

    /**
     * @inheritDoc
     * @throws \Paranoia\Core\Exception\CommunicationError
     */
    public function perform(RefundRequest $request): RefundResponse
    {
        $providerRequest = $this->requestBuilder->build($request);
        $providerResponse = $this->sendRequest($providerRequest);
        return  $this->responseParser->parse($providerResponse);
    }
}
