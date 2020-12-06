<?php
namespace Paranoia\Acquirer\Posnet\Service\Factory;

use Guzzle\Http\Client;
use Paranoia\Acquirer\Posnet\Formatter\CustomCurrencyCodeFormatter;
use Paranoia\Acquirer\Posnet\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\Posnet\PosnetConfiguration;
use Paranoia\Acquirer\Posnet\RequestBuilder\RefundRequestBuilder;
use Paranoia\Acquirer\Posnet\RequestBuilder\RequestBuilderCommon;
use Paranoia\Acquirer\Posnet\ResponseParser\RefundResponseParser;
use Paranoia\Acquirer\Posnet\ResponseParser\ResponseParserCommon;
use Paranoia\Acquirer\Posnet\Service\RefundServiceImp;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use Paranoia\Core\Acquirer\Service\RefundService;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Lib\HttpClient;
use Paranoia\Lib\XmlSerializer;

/**
 * Class RefundServiceFactory
 * @package Paranoia\Acquirer\Posnet\Service\Factory
 */
class RefundServiceFactory extends AbstractServiceFactory
{
    /** @var PosnetConfiguration */
    private $configuration;

    /**
     * RefundServiceFactory constructor.
     * @param PosnetConfiguration $configuration
     */
    public function __construct(PosnetConfiguration $configuration)
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
        $amountFormatter = new MoneyFormatter();
        $currencyFormatter = new CustomCurrencyCodeFormatter();
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