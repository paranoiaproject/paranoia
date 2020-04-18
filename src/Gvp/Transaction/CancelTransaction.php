<?php
namespace Paranoia\Gvp\Transaction;

use GuzzleHttp\Client;
use Paranoia\Configuration\GvpConfiguration;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\CommunicationError;
use Paranoia\Core\Exception\UnapprovedTransactionException;
use Paranoia\Core\Request\CancelRequest;
use Paranoia\Core\Response\CancelResponse;
use Paranoia\Core\Transaction\CancelTransaction as CoreCancelTransactionAlias;
use Paranoia\Gvp\RequestBuilder\CancelRequestBuilder;
use Paranoia\Gvp\ResponseParser\CancelResponseParser;

class CancelTransaction extends BaseTransaction implements CoreCancelTransactionAlias
{
    /** @var CancelRequestBuilder */
    private $requestBuilder;

    /** @var CancelResponseParser */
    private $responseParser;

    /**
     * CancelTransaction constructor.
     * @param GvpConfiguration $configuration
     * @param Client $client
     * @param CancelRequestBuilder $requestBuilder
     * @param CancelResponseParser $responseParser
     */
    public function __construct(
        GvpConfiguration $configuration,
        Client $client,
        CancelRequestBuilder $requestBuilder,
        CancelResponseParser $responseParser
    ) {
        parent::__construct($configuration, $client);
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
    }

    /**
     * @param CancelRequest $request
     * @return CancelResponse
     * @throws BadResponseException
     * @throws UnapprovedTransactionException
     * @throws CommunicationError
     */
    public function perform(CancelRequest $request): CancelResponse
    {
        $providerRequest = $this->requestBuilder->build($request);
        $providerResponse = $this->sendRequest($providerRequest);
        return  $this->responseParser->parse($providerResponse);
    }
}
