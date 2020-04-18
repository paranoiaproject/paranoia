<?php
namespace Paranoia\Gvp\Transaction;

use GuzzleHttp\Client;
use Paranoia\Configuration\GvpConfiguration;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\UnapprovedTransactionException;
use Paranoia\Core\Request\CaptureRequest;
use Paranoia\Core\Response\CaptureResponse;
use Paranoia\Core\Transaction\CaptureTransaction as CoreCaptureTransactionAlias;
use Paranoia\Gvp\RequestBuilder\CaptureRequestBuilder;
use Paranoia\Gvp\ResponseParser\CaptureResponseParser;

class CaptureTransaction extends BaseTransaction implements CoreCaptureTransactionAlias
{
    /** @var CaptureRequestBuilder */
    private $requestBuilder;

    /** @var CaptureResponseParser */
    private $responseParser;

    /**
     * CaptureTransaction constructor.
     * @param GvpConfiguration $configuration
     * @param Client $client
     * @param CaptureRequestBuilder $requestBuilder
     * @param CaptureResponseParser $responseParser
     */
    public function __construct(
        GvpConfiguration $configuration,
        Client $client,
        CaptureRequestBuilder $requestBuilder,
        CaptureResponseParser $responseParser
    ) {
        parent::__construct($configuration, $client);
        $this->requestBuilder = $requestBuilder;
        $this->responseParser = $responseParser;
    }

    /**
     * @param CaptureRequest $request
     * @return CaptureResponse
     * @throws BadResponseException
     * @throws UnapprovedTransactionException
     * @throws \Paranoia\Core\Exception\CommunicationError
     */
    public function perform(CaptureRequest $request): CaptureResponse
    {
        $providerRequest = $this->requestBuilder->build($request);
        $providerResponse = $this->sendRequest($providerRequest);
        return  $this->responseParser->parse($providerResponse);
    }
}
