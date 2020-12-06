<?php
namespace Paranoia\Acquirer\Gvp\Service\Factory;

use Guzzle\Http\Client;
use Paranoia\Acquirer\Gvp\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\Gvp\GvpConfiguration;
use Paranoia\Acquirer\Gvp\RequestBuilder\RefundRequestBuilder;
use Paranoia\Acquirer\Gvp\RequestBuilder\RequestBuilderCommon;
use Paranoia\Acquirer\Gvp\ResponseParser\RefundResponseParser;
use Paranoia\Acquirer\Gvp\ResponseParser\ResponseParserCommon;
use Paranoia\Acquirer\Gvp\Service\RefundServiceImp;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use Paranoia\Core\Acquirer\Service\RefundService;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Lib\HttpClient;
use Paranoia\Lib\XmlSerializer;

/**
 * Class RefundServiceFactory
 * @package Paranoia\Acquirer\Gvp\Service\Factory
 */
class RefundServiceFactory extends AbstractServiceFactory
{
    /** @var GvpConfiguration */
    private $configuration;

    /**
     * RefundServiceFactory constructor.
     * @param GvpConfiguration $configuration
     */
    public function __construct(GvpConfiguration $configuration)
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