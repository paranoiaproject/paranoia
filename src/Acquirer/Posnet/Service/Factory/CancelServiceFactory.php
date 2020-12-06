<?php
namespace Paranoia\Acquirer\Posnet\Service\Factory;

use Guzzle\Http\Client;
use Paranoia\Acquirer\Posnet\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\Posnet\PosnetConfiguration;
use Paranoia\Acquirer\Posnet\RequestBuilder\CancelRequestBuilder;
use Paranoia\Acquirer\Posnet\RequestBuilder\RequestBuilderCommon;
use Paranoia\Acquirer\Posnet\ResponseParser\CancelResponseParser;
use Paranoia\Acquirer\Posnet\ResponseParser\ResponseParserCommon;
use Paranoia\Acquirer\Posnet\Service\CancelServiceImp;
use Paranoia\Core\Acquirer\Service\CancelService;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use Paranoia\Lib\HttpClient;
use Paranoia\Lib\XmlSerializer;

/**
 * Class CancelServiceFactory
 * @package Paranoia\Acquirer\Posnet\Service\Factory
 */
class CancelServiceFactory extends AbstractServiceFactory
{
    /** @var PosnetConfiguration */
    private $configuration;

    /**
     * CancelServiceFactory constructor.
     * @param PosnetConfiguration $configuration
     */
    public function __construct(PosnetConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return CancelService
     */
    public function create(): CancelService
    {
        $requestBuilder = $this->createRequestBuilder();
        $responseParser = $this->createResponseParser();

        $httpClient = new HttpClient(new Client());

        return new CancelServiceImp($requestBuilder, $responseParser, $httpClient);
    }

    /**
     * @return CancelRequestBuilder
     */
    private function createRequestBuilder(): CancelRequestBuilder
    {
        $serializer = new XmlSerializer();
        $expireDateFormatter = new ExpireDateFormatter();
        $requestBuilderCommon = new RequestBuilderCommon($this->configuration, $expireDateFormatter);

        return new CancelRequestBuilder(
            $this->configuration,
            $requestBuilderCommon,
            $serializer
        );
    }

    /**
     * @return CancelResponseParser
     */
    private function createResponseParser(): CancelResponseParser
    {
        $responseParserCommon = new ResponseParserCommon();
        return new CancelResponseParser($responseParserCommon);
    }
}