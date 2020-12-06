<?php
namespace Paranoia\Acquirer\Posnet\Service\Factory;

use Guzzle\Http\Client;
use Paranoia\Acquirer\Posnet\Formatter\CustomCurrencyCodeFormatter;
use Paranoia\Acquirer\Posnet\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\Posnet\PosnetConfiguration;
use Paranoia\Acquirer\Posnet\RequestBuilder\CaptureRequestBuilder;
use Paranoia\Acquirer\Posnet\RequestBuilder\RequestBuilderCommon;
use Paranoia\Acquirer\Posnet\ResponseParser\CaptureResponseParser;
use Paranoia\Acquirer\Posnet\ResponseParser\ResponseParserCommon;
use Paranoia\Acquirer\Posnet\Service\CaptureServiceImp;
use Paranoia\Core\Acquirer\Service\CaptureService;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\MultiDigitInstallmentFormatter;
use Paranoia\Lib\HttpClient;
use Paranoia\Lib\XmlSerializer;

/**
 * Class CaptureServiceFactory
 * @package Paranoia\Acquirer\Posnet\Service\Factory
 */
class CaptureServiceFactory extends AbstractServiceFactory
{
    /** @var PosnetConfiguration */
    private $configuration;

    /**
     * CaptureServiceFactory constructor.
     * @param PosnetConfiguration $configuration
     */
    public function __construct(PosnetConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return CaptureService
     */
    public function create(): CaptureService
    {
        $requestBuilder = $this->createRequestBuilder();
        $responseParser = $this->createResponseParser();

        $httpClient = new HttpClient(new Client());

        return new CaptureServiceImp($requestBuilder, $responseParser, $httpClient);
    }

    /**
     * @return CaptureRequestBuilder
     */
    private function createRequestBuilder(): CaptureRequestBuilder
    {
        $serializer = new XmlSerializer();
        $expireDateFormatter = new ExpireDateFormatter();
        $amountFormatter = new MoneyFormatter();
        $currencyFormatter = new CustomCurrencyCodeFormatter();
        $installmentFormatter = new MultiDigitInstallmentFormatter();
        $requestBuilderCommon = new RequestBuilderCommon($this->configuration, $expireDateFormatter);

        return new CaptureRequestBuilder(
            $this->configuration,
            $requestBuilderCommon,
            $serializer,
            $amountFormatter,
            $currencyFormatter,
            $installmentFormatter
        );
    }

    /**
     * @return CaptureResponseParser
     */
    private function createResponseParser(): CaptureResponseParser
    {
        $responseParserCommon = new ResponseParserCommon();
        return new CaptureResponseParser($responseParserCommon);
    }
}