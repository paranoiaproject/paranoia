<?php
namespace Paranoia\Posnet;

use GuzzleHttp\Client;
use Paranoia\Configuration\PosnetConfiguration;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\MultiDigitInstallmentFormatter;
use Paranoia\Core\ProviderContext;
use Paranoia\Core\ProviderContextFactory;
use Paranoia\Core\Transformer\XmlTransformer;
use Paranoia\Posnet\Formatter\CustomCurrencyCodeFormatter;
use Paranoia\Posnet\Formatter\ExpireDateFormatter;
use Paranoia\Posnet\Formatter\OrderIdFormatter;
use Paranoia\Posnet\RequestBuilder\AuthorizationRequestBuilder;
use Paranoia\Posnet\RequestBuilder\CancelRequestBuilder;
use Paranoia\Posnet\RequestBuilder\CaptureRequestBuilder;
use Paranoia\Posnet\RequestBuilder\ChargeRequestBuilder;
use Paranoia\Posnet\RequestBuilder\RefundRequestBuilder;
use Paranoia\Posnet\ResponseParser\AuthorizationResponseParser;
use Paranoia\Posnet\ResponseParser\CancelResponseParser;
use Paranoia\Posnet\ResponseParser\CaptureResponseParser;
use Paranoia\Posnet\ResponseParser\ChargeResponseParser;
use Paranoia\Posnet\ResponseParser\RefundResponseParser;
use Paranoia\Posnet\Transaction\AuthorizationTransaction;
use Paranoia\Posnet\Transaction\CancelTransaction;
use Paranoia\Posnet\Transaction\CaptureTransaction;
use Paranoia\Posnet\Transaction\ChargeTransaction;
use Paranoia\Posnet\Transaction\RefundTransaction;

class PosnetContextFactory implements ProviderContextFactory
{
    /** @var PosnetConfiguration */
    private $configuration;

    /**
     * PosnetContextFactory constructor.
     * @param PosnetConfiguration $configuration
     */
    public function __construct(PosnetConfiguration $configuration)
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
            new MoneyFormatter(),
            new CustomCurrencyCodeFormatter(),
            new ExpireDateFormatter(),
            new MultiDigitInstallmentFormatter(),
            new OrderIdFormatter()
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
            new MoneyFormatter(),
            new CustomCurrencyCodeFormatter()
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
            new MoneyFormatter(),
            new CustomCurrencyCodeFormatter(),
            new ExpireDateFormatter(),
            new MultiDigitInstallmentFormatter(),
            new OrderIdFormatter()
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
            new MoneyFormatter(),
            new CustomCurrencyCodeFormatter()
        );

        $responseParser = new RefundResponseParser($transformer);

        return new RefundTransaction($this->configuration, $client, $requestBuilder, $responseParser);
    }
}
