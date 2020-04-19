<?php
namespace Paranoia\Posnet\Transaction;

use GuzzleHttp\Client;
use Paranoia\Configuration\PosnetConfiguration;
use Paranoia\Core\Request\AuthorizationRequest;
use Paranoia\Core\Response\AuthorizationResponse;
use Paranoia\Core\Transaction\AuthorizationTransaction as CoreAuthorizationTransactionAlias;
use Paranoia\Posnet\RequestBuilder\AuthorizationRequestBuilder;
use Paranoia\Posnet\ResponseParser\AuthorizationResponseParser;

class AuthorizationTransaction extends BaseTransaction implements CoreAuthorizationTransactionAlias
{
    /** @var AuthorizationRequestBuilder */
    protected $requestBuilder;

    /** @var AuthorizationResponseParser */
    protected $responseParser;

    /**
     * AuthorizationTransaction constructor.
     * @param PosnetConfiguration $configuration
     * @param Client $client
     * @param AuthorizationRequestBuilder $requestBuilder
     * @param AuthorizationResponseParser $responseParser
     */
    public function __construct(PosnetConfiguration $configuration, Client $client, AuthorizationRequestBuilder $requestBuilder, AuthorizationResponseParser $responseParser)
    {
        parent::__construct($configuration, $client);
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
    }

    /**
     * @inheritDoc
     * @throws \Paranoia\Core\Exception\CommunicationError
     */
    public function perform(AuthorizationRequest $request): AuthorizationResponse
    {
        $providerRequest = $this->requestBuilder->build($request);
        $providerResponse = $this->sendRequest($providerRequest);
        return  $this->responseParser->parse($providerResponse);
    }
}
