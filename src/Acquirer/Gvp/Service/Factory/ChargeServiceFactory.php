<?php
namespace Paranoia\Acquirer\Gvp\Service\Factory;

use Guzzle\Http\Client;
use Paranoia\Acquirer\Gvp\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\Gvp\GvpConfiguration;
use Paranoia\Acquirer\Gvp\RequestBuilder\ChargeRequestBuilder;
use Paranoia\Acquirer\Gvp\RequestBuilder\RequestBuilderCommon;
use Paranoia\Acquirer\Gvp\ResponseParser\ChargeResponseParser;
use Paranoia\Acquirer\Gvp\ResponseParser\ResponseParserCommon;
use Paranoia\Acquirer\Gvp\Service\ChargeServiceImp;
use Paranoia\Core\Acquirer\Service\ChargeService;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Lib\HttpClient;
use Paranoia\Lib\XmlSerializer;

/**
 * Class ChargeServiceFactory
 * @package Paranoia\Acquirer\Gvp\Service\Factory
 */
class ChargeServiceFactory extends AbstractServiceFactory
{
    /** @var GvpConfiguration */
    private $configuration;

    /**
     * ChargeServiceFactory constructor.
     * @param GvpConfiguration $configuration
     */
    public function __construct(GvpConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return ChargeService
     */
    public function create(): ChargeService
    {
        $requestBuilder = $this->createRequestBuilder();
        $responseParser = $this->createResponseParser();

        $httpClient = new HttpClient(new Client());

        return new ChargeServiceImp($requestBuilder, $responseParser, $httpClient);
    }

    /**
     * @return ChargeRequestBuilder
     */
    private function createRequestBuilder(): ChargeRequestBuilder
    {
        $serializer = new XmlSerializer();
        $expireDateFormatter = new ExpireDateFormatter();
        $amountFormatter = new MoneyFormatter();
        $currencyFormatter = new IsoNumericCurrencyCodeFormatter();
        $installmentFormatter = new SingleDigitInstallmentFormatter();
        $requestBuilderCommon = new RequestBuilderCommon($this->configuration, $expireDateFormatter);

        return new ChargeRequestBuilder(
            $this->configuration,
            $requestBuilderCommon,
            $serializer,
            $amountFormatter,
            $currencyFormatter,
            $installmentFormatter
        );
    }

    /**
     * @return ChargeResponseParser
     */
    private function createResponseParser(): ChargeResponseParser
    {
        $responseParserCommon = new ResponseParserCommon();
        return new ChargeResponseParser($responseParserCommon);
    }
}