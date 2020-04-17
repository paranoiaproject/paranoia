<?php
namespace Paranoia\Nestpay\Transaction;

use GuzzleHttp\ClientInterface;
use Paranoia\Configuration\NestpayConfiguration;
use Paranoia\Core\Request\AuthorizationRequest;
use Paranoia\Core\Response\AuthorizationResponse;
use Paranoia\Nestpay\RequestBuilder\AuthorizationRequestBuilder;
use Paranoia\Nestpay\ResponseParser\AuthorizationResponseParser;

class AuthorizationTransaction extends BaseTransaction
{
    /** @var AuthorizationRequestBuilder */
    private $requestBuilder;

    /** @var AuthorizationResponseParser */
    private $responseParser;

    public function __construct(NestpayConfiguration $configuration, ClientInterface $client, AuthorizationRequestBuilder $requestBuilder, AuthorizationResponseParser $responseParser)
    {
        parent::__construct($configuration, $client);
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
    }

    public function perform(AuthorizationRequest $request): AuthorizationResponse
    {

    }
}
