<?php
namespace Paranoia\Nestpay\Transaction;

use GuzzleHttp\Client;
use Paranoia\Configuration\NestpayConfiguration;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\UnapprovedTransactionException;
use Paranoia\Core\Request\CancelRequest;
use Paranoia\Core\Response\CancelResponse;
use Paranoia\Nestpay\RequestBuilder\CancelRequestBuilder;
use Paranoia\Nestpay\ResponseParser\CancelResponseParser;

class CancelTransaction extends BaseTransaction
{
    /** @var NestpayConfiguration */
    protected $configuration;

    /** @var CancelRequestBuilder */
    private $requestBuilder;

    /** @var CancelResponseParser */
    private $responseParser;

    /**
     * CancelTransaction constructor.
     * @param NestpayConfiguration $configuration
     * @param CancelRequestBuilder $requestBuilder
     * @param CancelResponseParser $responseParser
     */
    public function __construct(NestpayConfiguration $configuration, Client $client, CancelRequestBuilder $requestBuilder, CancelResponseParser $responseParser)
    {
        parent::__construct($configuration, $client);
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
    }

    /**
     * @param CancelRequest $request
     * @return CancelResponse
     * @throws BadResponseException
     * @throws UnapprovedTransactionException
     */
    public function perform(CancelRequest $request): CancelResponse
    {
        $providerRequest = $this->requestBuilder->build($request);
        $providerResponse = $this->sendRequest($providerRequest);
        return  $this->responseParser->parse($providerResponse);
    }
}
