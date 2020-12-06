<?php
namespace Paranoia\Acquirer\Gvp\Service\Factory;

use Guzzle\Http\Client;
use Paranoia\Acquirer\Gvp\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\Gvp\GvpConfiguration;
use Paranoia\Acquirer\Gvp\RequestBuilder\CancelRequestBuilder;
use Paranoia\Acquirer\Gvp\RequestBuilder\RequestBuilderCommon;
use Paranoia\Acquirer\Gvp\ResponseParser\CancelResponseParser;
use Paranoia\Acquirer\Gvp\ResponseParser\ResponseParserCommon;
use Paranoia\Acquirer\Gvp\Service\CancelServiceImp;
use Paranoia\Core\Acquirer\Service\CancelService;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use Paranoia\Lib\HttpClient;
use Paranoia\Lib\XmlSerializer;

class CancelServiceFactory extends AbstractServiceFactory
{
    /** @var GvpConfiguration */
    private $configuration;

    /**
     * CancelServiceFactory constructor.
     * @param GvpConfiguration $configuration
     */
    public function __construct(GvpConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function create(): CancelService
    {
        $requestBuilder = $this->createRequestBuilder();
        $responseParser = $this->createResponseParser();

        $httpClient = new HttpClient(new Client());

        return new CancelServiceImp($requestBuilder, $responseParser, $httpClient);
    }

    private function createRequestBuilder(): CancelRequestBuilder
    {
        $serializer = new XmlSerializer();
        $expireDateFormatter = new ExpireDateFormatter();
        $requestBuilderCommon = new RequestBuilderCommon($this->configuration, $expireDateFormatter);

        return new CancelRequestBuilder($this->configuration, $requestBuilderCommon, $serializer);
    }

    private function createResponseParser(): CancelResponseParser
    {
        $responseParserCommon = new ResponseParserCommon();
        return new CancelResponseParser($responseParserCommon);
    }
}