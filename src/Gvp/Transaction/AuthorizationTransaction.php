<?php
namespace Paranoia\Gvp\Transaction;

use GuzzleHttp\Client;
use Paranoia\Configuration\GvpConfiguration;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\CommunicationError;
use Paranoia\Core\Exception\UnapprovedTransactionException;
use Paranoia\Core\Request\AuthorizationRequest;
use Paranoia\Core\Response\AuthorizationResponse;
use Paranoia\Core\Transaction\AuthorizationTransaction as CoreAuthorizationTransactionAlias;
use Paranoia\Gvp\RequestBuilder\AuthorizationRequestBuilder;
use Paranoia\Gvp\ResponseParser\AuthorizationResponseParser;

class AuthorizationTransaction extends BaseTransaction implements CoreAuthorizationTransactionAlias
{
    /** @var AuthorizationRequestBuilder */
    private $requestBuilder;

    /** @var AuthorizationResponseParser */
    private $responseParser;

    /**
     * AuthorizationTransaction constructor.
     * @param GvpConfiguration $configuration
     * @param Client $client
     * @param AuthorizationRequestBuilder $requestBuilder
     * @param AuthorizationResponseParser $responseParser
     */
    public function __construct(
        GvpConfiguration $configuration,
        Client $client,
        AuthorizationRequestBuilder $requestBuilder,
        AuthorizationResponseParser $responseParser
    ) {
        parent::__construct($configuration, $client);
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
    }

    /**
     * @param AuthorizationRequest $request
     * @return AuthorizationResponse
     * @throws BadResponseException
     * @throws UnapprovedTransactionException
     * @throws CommunicationError
     */
    public function perform(AuthorizationRequest $request): AuthorizationResponse
    {
        $providerRequest = $this->requestBuilder->build($request);
        $providerResponse = $this->sendRequest($providerRequest);
        return  $this->responseParser->parse($providerResponse);
    }
}
