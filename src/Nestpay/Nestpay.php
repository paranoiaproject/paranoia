<?php
namespace Paranoia\Nestpay;

use GuzzleHttp\Client;
use Paranoia\Configuration\NestpayConfiguration;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\CommunicationError;
use Paranoia\Core\Exception\UnapprovedTransactionException;
use Paranoia\Core\Formatter\DecimalFormatter;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Core\Request\AuthorizationRequest;
use Paranoia\Core\Request\CancelRequest;
use Paranoia\Core\Request\CaptureRequest;
use Paranoia\Core\Request\ChargeRequest;
use Paranoia\Core\Request\RefundRequest;
use Paranoia\Core\Response\AuthorizationResponse;
use Paranoia\Core\Response\CancelResponse;
use Paranoia\Core\Response\CaptureResponse;
use Paranoia\Core\Response\ChargeResponse;
use Paranoia\Core\Response\RefundResponse;
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

class Nestpay
{
    /** @var NestpayConfiguration */
    private $configuration;

    /** @var Client */
    private $client;

    /** @var XmlTransformer */
    private $transformer;

    /** @var DecimalFormatter */
    private $amountFormatter;

    /** @var IsoNumericCurrencyCodeFormatter */
    private $currencyFormatter;

    /** @var ExpireDateFormatter */
    private $expireDateFormatter;

    /** @var SingleDigitInstallmentFormatter */
    private $installmentFormatter;

    /**
     * Nestpay constructor.
     * @param NestpayConfiguration $configuration
     * @param Client $client
     * @param XmlTransformer $transformer
     * @param DecimalFormatter $amountFormatter
     * @param IsoNumericCurrencyCodeFormatter $currencyFormatter
     * @param ExpireDateFormatter $expireDateFormatter
     * @param SingleDigitInstallmentFormatter $installmentFormatter
     */
    public function __construct(
        NestpayConfiguration $configuration,
        Client $client,
        XmlTransformer $transformer,
        DecimalFormatter $amountFormatter,
        IsoNumericCurrencyCodeFormatter $currencyFormatter,
        ExpireDateFormatter $expireDateFormatter,
        SingleDigitInstallmentFormatter $installmentFormatter
    ) {
        $this->configuration = $configuration;
        $this->client = $client;
        $this->transformer = $transformer;
        $this->amountFormatter = $amountFormatter;
        $this->currencyFormatter = $currencyFormatter;
        $this->expireDateFormatter = $expireDateFormatter;
        $this->installmentFormatter = $installmentFormatter;
    }

    /**
     * @param AuthorizationRequest $request
     * @return AuthorizationResponse
     * @throws BadResponseException
     * @throws CommunicationError
     * @throws UnapprovedTransactionException
     */
    public function authorization(AuthorizationRequest $request): AuthorizationResponse
    {
        $requestBuilder = new AuthorizationRequestBuilder(
            $this->configuration,
            $this->amountFormatter,
            $this->currencyFormatter,
            $this->expireDateFormatter,
            $this->installmentFormatter
        );

        $responseParser = new AuthorizationResponseParser($this->transformer);

        $transaction = new AuthorizationTransaction(
            $this->configuration,
            $this->client,
            $requestBuilder,
            $responseParser
        );

        return $transaction->perform($request);
    }

    /**
     * @param CaptureRequest $request
     * @return CaptureResponse
     * @throws BadResponseException
     * @throws CommunicationError
     * @throws UnapprovedTransactionException
     */
    public function capture(CaptureRequest $request): CaptureResponse
    {
        $requestBuilder = new CaptureRequestBuilder(
            $this->configuration,
            $this->amountFormatter,
            $this->currencyFormatter
        );

        $responseParser = new CaptureResponseParser($this->transformer);

        $transaction = new CaptureTransaction(
            $this->configuration,
            $this->client,
            $requestBuilder,
            $responseParser
        );

        return $transaction->perform($request);
    }

    /**
     * @param ChargeRequest $request
     * @return ChargeResponse
     * @throws BadResponseException
     * @throws CommunicationError
     * @throws UnapprovedTransactionException
     */
    public function charge(ChargeRequest $request): ChargeResponse
    {
        $requestBuilder = new ChargeRequestBuilder(
            $this->configuration,
            $this->amountFormatter,
            $this->currencyFormatter,
            $this->expireDateFormatter,
            $this->installmentFormatter
        );

        $responseParser = new ChargeResponseParser($this->transformer);

        $transaction = new ChargeTransaction(
            $this->configuration,
            $this->client,
            $requestBuilder,
            $responseParser
        );

        return $transaction->perform($request);
    }

    /**
     * @param CancelRequest $request
     * @return CancelResponse
     * @throws BadResponseException
     * @throws CommunicationError
     * @throws UnapprovedTransactionException
     */
    public function cancel(CancelRequest $request): CancelResponse
    {
        $requestBuilder = new CancelRequestBuilder($this->configuration);
        $responseParser = new CancelResponseParser($this->transformer);

        $transaction = new CancelTransaction(
            $this->configuration,
            $this->client,
            $requestBuilder,
            $responseParser
        );

        return $transaction->perform($request);
    }

    /**
     * @param RefundRequest $request
     * @return RefundResponse
     * @throws BadResponseException
     * @throws CommunicationError
     * @throws UnapprovedTransactionException
     */
    public function refund(RefundRequest $request): RefundResponse
    {
        $requestBuilder = new RefundRequestBuilder(
            $this->configuration,
            $this->amountFormatter,
            $this->currencyFormatter
        );

        $responseParser = new RefundResponseParser($this->transformer);

        $transaction = new RefundTransaction(
            $this->configuration,
            $this->client,
            $requestBuilder,
            $responseParser
        );

        return $transaction->perform($request);
    }
}
