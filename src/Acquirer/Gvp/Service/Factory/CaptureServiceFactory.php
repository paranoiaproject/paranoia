<?php
namespace Paranoia\Acquirer\Gvp\Service\Factory;

use Guzzle\Http\Client;
use Paranoia\Acquirer\Gvp\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\Gvp\GvpConfiguration;
use Paranoia\Acquirer\Gvp\RequestBuilder\CaptureRequestBuilder;
use Paranoia\Acquirer\Gvp\RequestBuilder\RequestBuilderCommon;
use Paranoia\Acquirer\Gvp\ResponseParser\CaptureResponseParser;
use Paranoia\Acquirer\Gvp\ResponseParser\ResponseParserCommon;
use Paranoia\Acquirer\Gvp\Service\CaptureServiceImp;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Lib\HttpClient;
use Paranoia\Lib\XmlSerializer;

/**
 * Class CaptureServiceFactory
 * @package Paranoia\Acquirer\Gvp\Service\Factory
 */
class CaptureServiceFactory extends AbstractServiceFactory
{
    /** @var GvpConfiguration */
    private $configuration;

    /**
     * CaptureServiceFactory constructor.
     * @param GvpConfiguration $configuration
     */
    public function __construct(GvpConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return CaptureServiceImp
     */
    public function create(): CaptureServiceImp
    {
        $requestBuilder = $this->createRequestBuilder();
        $responseParser = $this->createResponseParser();

        $httpClient = new HttpClient(new Client());

        return new CaptureServiceImp($requestBuilder, $responseParser, $httpClient);
    }

    /**
     * @return CaptureRequestBuilder
     */
    private function createRequestBuilder()
    {
        $serializer = new XmlSerializer();
        $expireDateFormatter = new ExpireDateFormatter();
        $amountFormatter = new MoneyFormatter();
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

    /**
     * @return CaptureResponseParser
     */
    private function createResponseParser()
    {
        $responseParserCommon = new ResponseParserCommon();
        return new CaptureResponseParser($responseParserCommon);
    }
}