<?php
namespace Paranoia\Nestpay\Transaction;

use GuzzleHttp\Client;
use Paranoia\Configuration\NestpayConfiguration;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\UnapprovedTransactionException;
use Paranoia\Core\Request\ChargeRequest;
use Paranoia\Core\Response\ChargeResponse;
use Paranoia\Nestpay\RequestBuilder\ChargeRequestBuilder;
use Paranoia\Nestpay\ResponseParser\ChargeResponseParser;

class ChargeTransaction extends BaseTransaction
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
     * @param Client $client
     * @param ChargeRequestBuilder $requestBuilder
     * @param ChargeResponseParser $responseParser
     */
    public function __construct(NestpayConfiguration $configuration, Client $client, ChargeRequestBuilder $requestBuilder, ChargeResponseParser $responseParser)
    {
        parent::__construct($configuration, $client);
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
    }

    /**
     * @param ChargeRequest $request
     * @return ChargeResponse
     * @throws BadResponseException
     * @throws UnapprovedTransactionException
     */
    public function perform(ChargeRequest $request): ChargeResponse
    {
        $providerRequest = $this->requestBuilder->build($request);
        $providerResponse = $this->sendRequest($providerRequest);
        return  $this->responseParser->parse($providerResponse);
    }
}
