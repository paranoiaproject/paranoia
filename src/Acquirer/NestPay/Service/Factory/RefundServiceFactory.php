<?php
namespace Paranoia\Acquirer\NestPay\Service\Factory;

use Guzzle\Http\Client;
use Paranoia\Acquirer\NestPay\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\NestPay\NestPayConfiguration;
use Paranoia\Acquirer\NestPay\RequestBuilder\RefundRequestBuilder;
use Paranoia\Acquirer\NestPay\RequestBuilder\RequestBuilderCommon;
use Paranoia\Acquirer\NestPay\ResponseParser\RefundResponseParser;
use Paranoia\Acquirer\NestPay\ResponseParser\ResponseParserCommon;
use Paranoia\Acquirer\NestPay\Service\RefundServiceImp;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use Paranoia\Core\Acquirer\Service\RefundService;
use Paranoia\Core\Formatter\DecimalFormatter;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Lib\HttpClient;
use Paranoia\Lib\XmlSerializer;

/**
 * Class RefundServiceFactory
 * @package Paranoia\Acquirer\NestPay\Service\Factory
 */
class RefundServiceFactory extends AbstractServiceFactory
{
    /** @var NestPayConfiguration */
    private $configuration;

    /**
     * RefundServiceFactory constructor.
     * @param NestPayConfiguration $configuration
     */
    public function __construct(NestPayConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return RefundService
     */
    public function create(): RefundService
    {
        $requestBuilder = $this->createRequestBuilder();
        $responseParser = $this->createResponseParser();

        $httpClient = new HttpClient(new Client());

        return new RefundServiceImp($requestBuilder, $responseParser, $httpClient);
    }

    /**
     * @return RefundRequestBuilder
     */
    private function createRequestBuilder(): RefundRequestBuilder
    {
        $serializer = new XmlSerializer();
        $expireDateFormatter = new ExpireDateFormatter();
        $amountFormatter = new DecimalFormatter();
        $currencyFormatter = new IsoNumericCurrencyCodeFormatter();
        $requestBuilderCommon = new RequestBuilderCommon($this->configuration, $expireDateFormatter);

        return new RefundRequestBuilder(
            $this->configuration,
            $requestBuilderCommon,
            $serializer,
            $amountFormatter,
            $currencyFormatter
        );
    }

    /**
     * @return RefundResponseParser
     */
    private function createResponseParser(): RefundResponseParser
    {
        $responseParserCommon = new ResponseParserCommon();
        return new RefundResponseParser($responseParserCommon);
    }
}