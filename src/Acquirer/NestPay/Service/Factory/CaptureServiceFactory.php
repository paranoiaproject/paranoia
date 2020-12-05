<?php
namespace Paranoia\Acquirer\NestPay\Service\Factory;

use Guzzle\Http\Client;
use Paranoia\Acquirer\NestPay\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\NestPay\NestPayConfiguration;
use Paranoia\Acquirer\NestPay\RequestBuilder\CaptureRequestBuilder;
use Paranoia\Acquirer\NestPay\RequestBuilder\RequestBuilderCommon;
use Paranoia\Acquirer\NestPay\ResponseParser\CaptureResponseParser;
use Paranoia\Acquirer\NestPay\ResponseParser\ResponseParserCommon;
use Paranoia\Acquirer\NestPay\Service\CaptureServiceImp;
use Paranoia\Core\Acquirer\Service\CaptureService;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use Paranoia\Core\Formatter\DecimalFormatter;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Lib\HttpClient;
use Paranoia\Lib\XmlSerializer;

/**
 * Class CaptureServiceFactory
 * @package Paranoia\Acquirer\NestPay\Service\Factory
 */
class CaptureServiceFactory extends AbstractServiceFactory
{
    /** @var NestPayConfiguration */
    private $configuration;

    /**
     * CaptureServiceFactory constructor.
     * @param NestPayConfiguration $configuration
     */
    public function __construct(NestPayConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function create(): CaptureService
    {
        $requestBuilder = $this->createRequestBuilder();
        $responseParser = $this->createResponseParser();

        $httpClient = new HttpClient(new Client());

        return new CaptureServiceImp($requestBuilder, $responseParser, $httpClient);
    }

    private function createRequestBuilder(): CaptureRequestBuilder
    {
        $serializer = new XmlSerializer();
        $expireDateFormatter = new ExpireDateFormatter();
        $amountFormatter = new DecimalFormatter();
        $currencyFormatter = new IsoNumericCurrencyCodeFormatter();
        $requestBuilderCommon = new RequestBuilderCommon($this->configuration, $expireDateFormatter);

        return new CaptureRequestBuilder(
            $this->configuration,
            $requestBuilderCommon,
            $serializer,
            $amountFormatter,
            $currencyFormatter
        );
    }

    private function createResponseParser(): CaptureResponseParser
    {
        $responseParserCommon = new ResponseParserCommon();
        return new CaptureResponseParser($responseParserCommon);
    }
}