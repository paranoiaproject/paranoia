<?php
namespace Paranoia\Acquirer\Posnet\Service\Factory;

use Guzzle\Http\Client;
use Paranoia\Acquirer\Posnet\Formatter\CustomCurrencyCodeFormatter;
use Paranoia\Acquirer\Posnet\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\Posnet\Formatter\OrderIdFormatter;
use Paranoia\Acquirer\Posnet\PosnetConfiguration;
use Paranoia\Acquirer\Posnet\RequestBuilder\AuthorizationRequestBuilder;
use Paranoia\Acquirer\Posnet\RequestBuilder\RequestBuilderCommon;
use Paranoia\Acquirer\Posnet\ResponseParser\AuthorizationResponseParser;
use Paranoia\Acquirer\Posnet\ResponseParser\ResponseParserCommon;
use Paranoia\Acquirer\Posnet\Service\AuthorizationServiceImp;
use Paranoia\Core\Acquirer\Service\AuthorizationService;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\MultiDigitInstallmentFormatter;
use Paranoia\Lib\HttpClient;
use Paranoia\Lib\XmlSerializer;

/**
 * Class AuthorizationServiceFactory
 * @package Paranoia\Acquirer\Posnet\Service\Factory
 */
class AuthorizationServiceFactory extends AbstractServiceFactory
{
    /** @var PosnetConfiguration */
    private $configuration;

    /**
     * AuthorizationServiceFactory constructor.
     * @param PosnetConfiguration $configuration
     */
    public function __construct(PosnetConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return AuthorizationService
     */
    public function create(): AuthorizationService
    {
        $requestBuilder = $this->createRequestBuilder();
        $responseParser = $this->createResponseParser();

        $httpClient = new HttpClient(new Client());

        return new AuthorizationServiceImp($requestBuilder, $responseParser, $httpClient);
    }

    /**
     * @return AuthorizationRequestBuilder
     */
    private function createRequestBuilder(): AuthorizationRequestBuilder
    {
        $serializer = new XmlSerializer();
        $expireDateFormatter = new ExpireDateFormatter();
        $amountFormatter = new MoneyFormatter();
        $currencyFormatter = new CustomCurrencyCodeFormatter();
        $installmentFormatter = new MultiDigitInstallmentFormatter();
        $requestBuilderCommon = new RequestBuilderCommon($this->configuration, $expireDateFormatter);
        $orderIdFormatter = new OrderIdFormatter();

        return new AuthorizationRequestBuilder(
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
     * @return AuthorizationResponseParser
     */
    private function createResponseParser(): AuthorizationResponseParser
    {
        $responseParserCommon = new ResponseParserCommon();
        return new AuthorizationResponseParser($responseParserCommon);
    }
}