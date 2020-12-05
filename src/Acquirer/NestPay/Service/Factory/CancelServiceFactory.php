<?php
namespace Paranoia\Acquirer\NestPay\Service\Factory;

use Guzzle\Http\Client;
use Paranoia\Acquirer\NestPay\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\NestPay\NestPayConfiguration;
use Paranoia\Acquirer\NestPay\RequestBuilder\CancelRequestBuilder;
use Paranoia\Acquirer\NestPay\RequestBuilder\RequestBuilderCommon;
use Paranoia\Acquirer\NestPay\ResponseParser\CancelResponseParser;
use Paranoia\Acquirer\NestPay\ResponseParser\ResponseParserCommon;
use Paranoia\Acquirer\NestPay\Service\CancelServiceImp;
use Paranoia\Core\Acquirer\Service\CancelService;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use Paranoia\Lib\HttpClient;
use Paranoia\Lib\XmlSerializer;

/**
 * Class CancelServiceFactory
 * @package Paranoia\Acquirer\NestPay\Service\Factory
 */
class CancelServiceFactory extends AbstractServiceFactory
{
    /** @var NestPayConfiguration */
    private $configuration;

    /**
     * CancelServiceFactory constructor.
     * @param NestPayConfiguration $configuration
     */
    public function __construct(NestPayConfiguration $configuration)
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