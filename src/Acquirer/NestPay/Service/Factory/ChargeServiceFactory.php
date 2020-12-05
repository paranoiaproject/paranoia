<?php
namespace Paranoia\Acquirer\NestPay\Service\Factory;

use Guzzle\Http\Client;
use Paranoia\Acquirer\NestPay\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\NestPay\NestPayConfiguration;
use Paranoia\Acquirer\NestPay\RequestBuilder\ChargeRequestBuilder;
use Paranoia\Acquirer\NestPay\RequestBuilder\RequestBuilderCommon;
use Paranoia\Acquirer\NestPay\ResponseParser\ChargeResponseParser;
use Paranoia\Acquirer\NestPay\ResponseParser\ResponseParserCommon;
use Paranoia\Acquirer\NestPay\Service\ChargeServiceImp;
use Paranoia\Core\Acquirer\Service\ChargeService;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use Paranoia\Core\Formatter\DecimalFormatter;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Lib\HttpClient;
use Paranoia\Lib\XmlSerializer;

/**
 * Class ChargeServiceFactory
 * @package Paranoia\Acquirer\NestPay\Service\Factory
 */
class ChargeServiceFactory extends AbstractServiceFactory
{
    /** @var NestPayConfiguration */
    private $configuration;

    /**
     * ChargeServiceFactory constructor.
     * @param NestPayConfiguration $configuration
     */
    public function __construct(NestPayConfiguration $configuration)
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
        $amountFormatter = new DecimalFormatter();
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