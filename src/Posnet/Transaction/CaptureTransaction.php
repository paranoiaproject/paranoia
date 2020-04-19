<?php
namespace Paranoia\Posnet\Transaction;

use GuzzleHttp\Client;
use Paranoia\Configuration\PosnetConfiguration;
use Paranoia\Core\Request\CaptureRequest;
use Paranoia\Core\Response\CaptureResponse;
use Paranoia\Core\Transaction\CaptureTransaction as CoreCaptureTransactionAlias;
use Paranoia\Posnet\RequestBuilder\CaptureRequestBuilder;
use Paranoia\Posnet\ResponseParser\CaptureResponseParser;

class CaptureTransaction extends BaseTransaction implements CoreCaptureTransactionAlias
{
    /** @var CaptureRequestBuilder */
    protected $requestBuilder;

    /** @var CaptureResponseParser */
    protected $responseParser;

    /**
     * CaptureTransaction constructor.
     * @param PosnetConfiguration $configuration
     * @param Client $client
     * @param CaptureRequestBuilder $requestBuilder
     * @param CaptureResponseParser $responseParser
     */
    public function __construct(PosnetConfiguration $configuration, Client $client, CaptureRequestBuilder $requestBuilder, CaptureResponseParser $responseParser)
    {
        parent::__construct($configuration, $client);
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
    }

    /**
     * @inheritDoc
     * @throws \Paranoia\Core\Exception\CommunicationError
     */
    public function perform(CaptureRequest $request): CaptureResponse
    {
        $providerRequest = $this->requestBuilder->build($request);
        $providerResponse = $this->sendRequest($providerRequest);
        return  $this->responseParser->parse($providerResponse);
    }
}
