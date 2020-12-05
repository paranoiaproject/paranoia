<?php
namespace Paranoia\Acquirer\NestPay\Service\Factory;

use Guzzle\Http\Client;
use Paranoia\Acquirer\NestPay\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\NestPay\NestPayConfiguration;
use Paranoia\Acquirer\NestPay\RequestBuilder\AuthorizationRequestBuilder;
use Paranoia\Acquirer\NestPay\RequestBuilder\RequestBuilderCommon;
use Paranoia\Acquirer\NestPay\ResponseParser\AuthorizationResponseParser;
use Paranoia\Acquirer\NestPay\ResponseParser\ResponseParserCommon;
use Paranoia\Acquirer\NestPay\Service\AuthorizationServiceImp;
use Paranoia\Core\Acquirer\Service\AuthorizationService;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use Paranoia\Core\Formatter\DecimalFormatter;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Lib\HttpClient;
use Paranoia\Lib\XmlSerializer;

/**
 * Class AuthorizationServiceFactory
 * @package Paranoia\Acquirer\NestPay\Service\Factory
 */
class AuthorizationServiceFactory extends AbstractServiceFactory
{
    /** @var NestPayConfiguration */
    private $configuration;

    /**
     * AuthorizationServiceFactory constructor.
     * @param NestPayConfiguration $configuration
     */
    public function __construct(NestPayConfiguration $configuration)
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
        $amountFormatter = new DecimalFormatter();
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