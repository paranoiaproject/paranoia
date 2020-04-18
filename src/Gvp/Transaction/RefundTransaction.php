<?php
namespace Paranoia\Gvp\Transaction;

use GuzzleHttp\Client;
use Paranoia\Configuration\GvpConfiguration;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\CommunicationError;
use Paranoia\Core\Exception\UnapprovedTransactionException;
use Paranoia\Core\Request\RefundRequest;
use Paranoia\Core\Response\RefundResponse;
use Paranoia\Core\Transaction\RefundTransaction as CoreRefundTransactionAlias;
use Paranoia\Gvp\RequestBuilder\RefundRequestBuilder;
use Paranoia\Gvp\ResponseParser\RefundResponseParser;

class RefundTransaction extends BaseTransaction implements CoreRefundTransactionAlias
{
    /** @var RefundRequestBuilder */
    private $requestBuilder;

    /** @var RefundResponseParser */
    private $responseParser;

    /**
     * RefundTransaction constructor.
     * @param GvpConfiguration $configuration
     * @param Client $client
     * @param RefundRequestBuilder $requestBuilder
     * @param RefundResponseParser $responseParser
     */
    public function __construct(
        GvpConfiguration $configuration,
        Client $client,
        RefundRequestBuilder $requestBuilder,
        RefundResponseParser $responseParser
    ) {
        parent::__construct($configuration, $client);
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
    }

    /**
     * @param RefundRequest $request
     * @return RefundResponse
     * @throws BadResponseException
     * @throws UnapprovedTransactionException
     * @throws CommunicationError
     */
    public function perform(RefundRequest $request): RefundResponse
    {
        $providerRequest = $this->requestBuilder->build($request);
        $providerResponse = $this->sendRequest($providerRequest);
        return  $this->responseParser->parse($providerResponse);
    }
}
