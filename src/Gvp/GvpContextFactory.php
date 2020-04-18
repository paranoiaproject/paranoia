<?php
namespace Paranoia\Gvp;

use GuzzleHttp\Client;
use Paranoia\Configuration\GvpConfiguration;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Core\ProviderContext;
use Paranoia\Core\Transformer\XmlTransformer;
use Paranoia\Gvp\Formatter\ExpireDateFormatter;
use Paranoia\Gvp\RequestBuilder\AuthorizationRequestBuilder;
use Paranoia\Gvp\RequestBuilder\CancelRequestBuilder;
use Paranoia\Gvp\RequestBuilder\CaptureRequestBuilder;
use Paranoia\Gvp\RequestBuilder\ChargeRequestBuilder;
use Paranoia\Gvp\RequestBuilder\RefundRequestBuilder;
use Paranoia\Gvp\ResponseParser\AuthorizationResponseParser;
use Paranoia\Gvp\ResponseParser\CancelResponseParser;
use Paranoia\Gvp\ResponseParser\CaptureResponseParser;
use Paranoia\Gvp\ResponseParser\ChargeResponseParser;
use Paranoia\Gvp\ResponseParser\RefundResponseParser;
use Paranoia\Gvp\Transaction\AuthorizationTransaction;
use Paranoia\Gvp\Transaction\CancelTransaction;
use Paranoia\Gvp\Transaction\CaptureTransaction;
use Paranoia\Gvp\Transaction\ChargeTransaction;
use Paranoia\Gvp\Transaction\RefundTransaction;

class GvpContextFactory
{
    /** @var GvpConfiguration */
    private $configuration;

    /**
     * GvpContextFactory constructor.
     * @param GvpConfiguration $configuration
     */
    public function __construct(GvpConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return ProviderContext
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
            new MoneyFormatter(),
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
            new MoneyFormatter(),
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
            new MoneyFormatter(),
            new IsoNumericCurrencyCodeFormatter()
        );

        $responseParser = new RefundResponseParser($transformer);

        return new RefundTransaction($this->configuration, $client, $requestBuilder, $responseParser);
    }
}
