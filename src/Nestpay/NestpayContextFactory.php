<?php
namespace Paranoia\Nestpay;

use GuzzleHttp\Client;
use Paranoia\Configuration\NestpayConfiguration;
use Paranoia\Core\Formatter\DecimalFormatter;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Core\ProviderContext;
use Paranoia\Core\ProviderContextFactory;
use Paranoia\Core\Transformer\XmlTransformer;
use Paranoia\Nestpay\Formatter\ExpireDateFormatter;
use Paranoia\Nestpay\RequestBuilder\AuthorizationRequestBuilder;
use Paranoia\Nestpay\RequestBuilder\CancelRequestBuilder;
use Paranoia\Nestpay\RequestBuilder\CaptureRequestBuilder;
use Paranoia\Nestpay\RequestBuilder\ChargeRequestBuilder;
use Paranoia\Nestpay\RequestBuilder\RefundRequestBuilder;
use Paranoia\Nestpay\ResponseParser\AuthorizationResponseParser;
use Paranoia\Nestpay\ResponseParser\CancelResponseParser;
use Paranoia\Nestpay\ResponseParser\CaptureResponseParser;
use Paranoia\Nestpay\ResponseParser\ChargeResponseParser;
use Paranoia\Nestpay\ResponseParser\RefundResponseParser;
use Paranoia\Nestpay\Transaction\AuthorizationTransaction;
use Paranoia\Nestpay\Transaction\CancelTransaction;
use Paranoia\Nestpay\Transaction\CaptureTransaction;
use Paranoia\Nestpay\Transaction\ChargeTransaction;
use Paranoia\Nestpay\Transaction\RefundTransaction;

class NestpayContextFactory implements ProviderContextFactory
{
    /** @var NestpayConfiguration */
    private $configuration;

    /**
     * NestpayContextFactory constructor.
     * @param NestpayConfiguration $configuration
     */
    public function __construct(NestpayConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @inheritDoc
     */
    public function createContext(): ProviderContext
    {
        $client = new Client();
        $transformer = new XmlTransformer();

        return new ProviderContext(
            $this->createAuthorizationTransaction($transformer, $client),
            $this->createCaptureTransaction($transformer, $client),
            $this->createChargeTransaction($transformer, $client),
            $this->createCancelTransaction($transformer, $client),
            $this->createRefundTransaction($transformer, $client)
        );
    }

    /**
     * @param XmlTransformer $transformer
     * @param Client $client
     * @return AuthorizationTransaction
     */
    private function createAuthorizationTransaction(XmlTransformer $transformer, Client $client): AuthorizationTransaction
    {
        $requestBuilder = new AuthorizationRequestBuilder(
            $this->configuration,
            new DecimalFormatter(),
            new IsoNumericCurrencyCodeFormatter(),
            new ExpireDateFormatter(),
            new SingleDigitInstallmentFormatter()
        );

        $responseParser = new AuthorizationResponseParser($transformer);

        return new AuthorizationTransaction($this->configuration, $client, $requestBuilder, $responseParser);
    }

    /**
     * @param XmlTransformer $transformer
     * @param Client $client
     * @return CaptureTransaction
     */
    private function createCaptureTransaction(XmlTransformer $transformer, Client $client): CaptureTransaction
    {
        $requestBuilder = new CaptureRequestBuilder(
            $this->configuration,
            new DecimalFormatter(),
            new IsoNumericCurrencyCodeFormatter()
        );

        $responseParser = new CaptureResponseParser($transformer);

        return new CaptureTransaction($this->configuration, $client, $requestBuilder, $responseParser);
    }

    /**
     * @param XmlTransformer $transformer
     * @param Client $client
     * @return ChargeTransaction
     */
    private function createChargeTransaction(XmlTransformer $transformer, Client $client): ChargeTransaction
    {
        $requestBuilder = new ChargeRequestBuilder(
            $this->configuration,
            new DecimalFormatter(),
            new IsoNumericCurrencyCodeFormatter(),
            new ExpireDateFormatter(),
            new SingleDigitInstallmentFormatter()
        );

        $responseParser = new ChargeResponseParser($transformer);

        return new ChargeTransaction($this->configuration, $client, $requestBuilder, $responseParser);
    }

    /**
     * @param XmlTransformer $transformer
     * @param Client $client
     * @return CancelTransaction
     */
    private function createCancelTransaction(XmlTransformer $transformer, Client $client): CancelTransaction
    {
        $requestBuilder = new CancelRequestBuilder($this->configuration);
        $responseParser = new CancelResponseParser($transformer);
        return new CancelTransaction($this->configuration, $client, $requestBuilder, $responseParser);
    }

    /**
     * @param XmlTransformer $transformer
     * @param Client $client
     * @return RefundTransaction
     */
    private function createRefundTransaction(XmlTransformer $transformer, Client $client): RefundTransaction
    {
        $requestBuilder = new RefundRequestBuilder(
            $this->configuration,
            new DecimalFormatter(),
            new IsoNumericCurrencyCodeFormatter()
        );

        $responseParser = new RefundResponseParser($transformer);

        return new RefundTransaction($this->configuration, $client, $requestBuilder, $responseParser);
    }
}
