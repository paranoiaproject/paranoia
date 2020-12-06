<?php
namespace Paranoia\Acquirer\Gvp\Service\Factory;

use Guzzle\Http\Client;
use Paranoia\Acquirer\Gvp\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\Gvp\GvpConfiguration;
use Paranoia\Acquirer\Gvp\RequestBuilder\AuthorizationRequestBuilder;
use Paranoia\Acquirer\Gvp\RequestBuilder\RequestBuilderCommon;
use Paranoia\Acquirer\Gvp\ResponseParser\AuthorizationResponseParser;
use Paranoia\Acquirer\Gvp\ResponseParser\ResponseParserCommon;
use Paranoia\Acquirer\Gvp\Service\AuthorizationServiceImp;
use Paranoia\Core\Acquirer\Service\AuthorizationService;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Lib\HttpClient;
use Paranoia\Lib\XmlSerializer;

/**
 * Class AuthorizationServiceFactory
 * @package Paranoia\Acquirer\Gvp\Service\Factory
 */
class AuthorizationServiceFactory extends AbstractServiceFactory
{
    /** @var GvpConfiguration */
    private $configuration;

    /**
     * AuthorizationServiceFactory constructor.
     * @param GvpConfiguration $configuration
     */
    public function __construct(GvpConfiguration $configuration)
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
        $currencyFormatter = new IsoNumericCurrencyCodeFormatter();
        $installmentFormatter = new SingleDigitInstallmentFormatter();
        $requestBuilderCommon = new RequestBuilderCommon($this->configuration, $expireDateFormatter);

        return new AuthorizationRequestBuilder(
            $this->configuration,
            $requestBuilderCommon,
            $serializer,
            $amountFormatter,
            $currencyFormatter,
            $installmentFormatter
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