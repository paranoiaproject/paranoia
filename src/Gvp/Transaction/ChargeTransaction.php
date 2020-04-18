<?php
namespace Paranoia\Gvp\Transaction;

use GuzzleHttp\Client;
use Paranoia\Configuration\GvpConfiguration;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\CommunicationError;
use Paranoia\Core\Exception\UnapprovedTransactionException;
use Paranoia\Core\Request\ChargeRequest;
use Paranoia\Core\Response\ChargeResponse;
use Paranoia\Core\Transaction\ChargeTransaction as CoreChargeTransactionAlias;
use Paranoia\Gvp\RequestBuilder\ChargeRequestBuilder;
use Paranoia\Gvp\ResponseParser\ChargeResponseParser;

class ChargeTransaction extends BaseTransaction implements CoreChargeTransactionAlias
{
    /** @var ChargeRequestBuilder */
    private $requestBuilder;

    /** @var ChargeResponseParser */
    private $responseParser;

    /**
     * ChargeTransaction constructor.
     * @param GvpConfiguration $configuration
     * @param Client $client
     * @param ChargeRequestBuilder $requestBuilder
     * @param ChargeResponseParser $responseParser
     */
    public function __construct(
        GvpConfiguration $configuration,
        Client $client,
        ChargeRequestBuilder $requestBuilder,
        ChargeResponseParser $responseParser
    ) {
        parent::__construct($configuration, $client);
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
    }

    /**
     * @param ChargeRequest $request
     * @return ChargeResponse
     * @throws BadResponseException
     * @throws UnapprovedTransactionException
     * @throws CommunicationError
     */
    public function perform(ChargeRequest $request): ChargeResponse
    {
        $providerRequest = $this->requestBuilder->build($request);
        $providerResponse = $this->sendRequest($providerRequest);
        return  $this->responseParser->parse($providerResponse);
    }
}
