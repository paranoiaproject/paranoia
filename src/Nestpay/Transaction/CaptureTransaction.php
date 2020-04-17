<?php
namespace Paranoia\Nestpay\Transaction;

use GuzzleHttp\Client;
use Paranoia\Configuration\NestpayConfiguration;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\UnapprovedTransactionException;
use Paranoia\Core\Request\CaptureRequest;
use Paranoia\Core\Response\CaptureResponse;
use Paranoia\Nestpay\RequestBuilder\CaptureRequestBuilder;
use Paranoia\Nestpay\ResponseParser\CaptureResponseParser;

class CaptureTransaction extends BaseTransaction
{
    /** @var CaptureRequestBuilder */
    private $requestBuilder;

    /** @var CaptureResponseParser */
    private $responseParser;

    /**
     * CaptureTransaction constructor.
     * @param NestpayConfiguration $configuration
     * @param Client $client
     * @param CaptureRequestBuilder $requestBuilder
     * @param CaptureResponseParser $responseParser
     */
    public function __construct(NestpayConfiguration $configuration, Client $client, CaptureRequestBuilder $requestBuilder, CaptureResponseParser $responseParser)
    {
        parent::__construct($configuration, $client);
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
    }

    /**
     * @param CaptureRequest $request
     * @return CaptureResponse
     * @throws BadResponseException
     * @throws UnapprovedTransactionException
     */
    public function perform(CaptureRequest $request): CaptureResponse
    {
        $providerRequest = $this->requestBuilder->build($request);
        $providerResponse = $this->sendRequest($providerRequest);
        return  $this->responseParser->parse($providerResponse);
    }
}
