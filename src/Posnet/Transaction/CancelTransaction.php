<?php
namespace Paranoia\Posnet\Transaction;

use GuzzleHttp\Client;
use Paranoia\Configuration\PosnetConfiguration;
use Paranoia\Core\Request\CancelRequest;
use Paranoia\Core\Response\CancelResponse;
use Paranoia\Core\Transaction\CancelTransaction as CoreCancelTransactionAlias;
use Paranoia\Posnet\RequestBuilder\CancelRequestBuilder;
use Paranoia\Posnet\ResponseParser\CancelResponseParser;

class CancelTransaction extends BaseTransaction implements CoreCancelTransactionAlias
{
    /** @var CancelRequestBuilder */
    protected $requestBuilder;

    /** @var CancelResponseParser */
    protected $responseParser;

    /**
     * CancelTransaction constructor.
     * @param PosnetConfiguration $configuration
     * @param Client $client
     * @param CancelRequestBuilder $requestBuilder
     * @param CancelResponseParser $responseParser
     */
    public function __construct(PosnetConfiguration $configuration, Client $client, CancelRequestBuilder $requestBuilder, CancelResponseParser $responseParser)
    {
        parent::__construct($configuration, $client);
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
    }

    /**
     * @inheritDoc
     * @throws \Paranoia\Core\Exception\CommunicationError
     */
    public function perform(CancelRequest $request): CancelResponse
    {
        $providerRequest = $this->requestBuilder->build($request);
        $providerResponse = $this->sendRequest($providerRequest);
        return  $this->responseParser->parse($providerResponse);
    }
}
