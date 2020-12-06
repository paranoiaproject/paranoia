<?php
namespace Paranoia\Acquirer\Posnet\Service\Factory;

use Guzzle\Http\Client;
use Paranoia\Acquirer\Posnet\Formatter\CustomCurrencyCodeFormatter;
use Paranoia\Acquirer\Posnet\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\Posnet\Formatter\OrderIdFormatter;
use Paranoia\Acquirer\Posnet\PosnetConfiguration;
use Paranoia\Acquirer\Posnet\RequestBuilder\ChargeRequestBuilder;
use Paranoia\Acquirer\Posnet\RequestBuilder\RequestBuilderCommon;
use Paranoia\Acquirer\Posnet\ResponseParser\ChargeResponseParser;
use Paranoia\Acquirer\Posnet\ResponseParser\ResponseParserCommon;
use Paranoia\Acquirer\Posnet\Service\ChargeServiceImp;
use Paranoia\Core\Acquirer\Service\ChargeService;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\MultiDigitInstallmentFormatter;
use Paranoia\Lib\HttpClient;
use Paranoia\Lib\XmlSerializer;

/**
 * Class ChargeServiceFactory
 * @package Paranoia\Acquirer\Posnet\Service\Factory
 */
class ChargeServiceFactory extends AbstractServiceFactory
{
    /** @var PosnetConfiguration */
    private $configuration;

    /**
     * ChargeServiceFactory constructor.
     * @param PosnetConfiguration $configuration
     */
    public function __construct(PosnetConfiguration $configuration)
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
        $currencyFormatter = new CustomCurrencyCodeFormatter();
        $installmentFormatter = new MultiDigitInstallmentFormatter();
        $requestBuilderCommon = new RequestBuilderCommon($this->configuration, $expireDateFormatter);
        $orderIdFormatter = new OrderIdFormatter();

        return new ChargeRequestBuilder(
            $this->configuration,
            $requestBuilderCommon,
            $serializer,
            $amountFormatter,
            $currencyFormatter,
            $installmentFormatter,
            $orderIdFormatter
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