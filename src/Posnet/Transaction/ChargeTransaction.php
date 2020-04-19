<?php
namespace Paranoia\Posnet\Transaction;

use GuzzleHttp\Client;
use Paranoia\Configuration\PosnetConfiguration;
use Paranoia\Core\Request\ChargeRequest;
use Paranoia\Core\Response\ChargeResponse;
use Paranoia\Core\Transaction\ChargeTransaction as CoreChargeTransactionAlias;
use Paranoia\Posnet\RequestBuilder\ChargeRequestBuilder;
use Paranoia\Posnet\ResponseParser\ChargeResponseParser;

class ChargeTransaction extends BaseTransaction implements CoreChargeTransactionAlias
{
    /** @var ChargeRequestBuilder */
    protected $requestBuilder;

    /** @var ChargeResponseParser */
    protected $responseParser;

    /**
     * ChargeTransaction constructor.
     * @param PosnetConfiguration $configuration
     * @param Client $client
     * @param ChargeRequestBuilder $requestBuilder
     * @param ChargeResponseParser $responseParser
     */
    public function __construct(PosnetConfiguration $configuration, Client $client, ChargeRequestBuilder $requestBuilder, ChargeResponseParser $responseParser)
    {
        parent::__construct($configuration, $client);
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
    }

    /**
     * @inheritDoc
     * @throws \Paranoia\Core\Exception\CommunicationError
     */
    public function perform(ChargeRequest $request): ChargeResponse
    {
        $providerRequest = $this->requestBuilder->build($request);
        $providerResponse = $this->sendRequest($providerRequest);
        return  $this->responseParser->parse($providerResponse);
    }
}
