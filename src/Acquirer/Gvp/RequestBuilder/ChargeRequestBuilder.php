<?php
namespace Paranoia\Acquirer\Gvp\RequestBuilder;

use Paranoia\Acquirer\Gvp\GvpConfiguration;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Core\Model\Request\ChargeRequest;
use Paranoia\Core\Model\Request\HttpRequest;
use Paranoia\Lib\XmlSerializer;

/**
 * Class ChargeRequestBuilder
 * @package Paranoia\Acquirer\Gvp\RequestBuilder
 */
class ChargeRequestBuilder
{
    private const TRANSACTION_TYPE = 'sales';

    /** @var GvpConfiguration */
    private $configuration;

    /** @var RequestBuilderCommon */
    private $requestBuilderCommon;

    /** @var XmlSerializer */
    protected $serializer;

    /** @var MoneyFormatter */
    protected $amountFormatter;

    /** @var  IsoNumericCurrencyCodeFormatter */
    protected $currencyCodeFormatter;

    /** @var  SingleDigitInstallmentFormatter */
    protected $installmentFormatter;

    /**
     * ChargeRequestBuilder constructor.
     * @param GvpConfiguration $configuration
     * @param RequestBuilderCommon $requestBuilderCommon
     * @param XmlSerializer $serializer
     * @param MoneyFormatter $amountFormatter
     * @param IsoNumericCurrencyCodeFormatter $currencyCodeFormatter
     * @param SingleDigitInstallmentFormatter $installmentFormatter
     */
    public function __construct(
        GvpConfiguration $configuration,
        RequestBuilderCommon $requestBuilderCommon,
        XmlSerializer $serializer,
        MoneyFormatter $amountFormatter,
        IsoNumericCurrencyCodeFormatter $currencyCodeFormatter,
        SingleDigitInstallmentFormatter $installmentFormatter
    ) {
        $this->configuration = $configuration;
        $this->requestBuilderCommon = $requestBuilderCommon;
        $this->serializer = $serializer;
        $this->amountFormatter = $amountFormatter;
        $this->currencyCodeFormatter = $currencyCodeFormatter;
        $this->installmentFormatter = $installmentFormatter;
    }

    /**
     * @param ChargeRequest $request
     * @return HttpRequest
     */
    public function build(ChargeRequest $request): HttpRequest
    {
        $headers = $this->requestBuilderCommon->buildHeaders();
        $body = $this->buildBody($request);

        return new HttpRequest($this->configuration->getApiUrl(), HttpRequest::HTTP_POST, $headers, $body);
    }

    /**
     * @param ChargeRequest $request
     * @return string
     */
    private function buildBody(ChargeRequest $request): string
    {
        $hash = $this->requestBuilderCommon->buildHashWithCard(
            $request->getCard(),
            $request->getOrderId(),
            $this->amountFormatter->format($request->getAmount()),
            $this->configuration->getAuthorizationPassword()
        );

        $terminal = $this->requestBuilderCommon->buildTerminal($this->configuration->getAuthorizationUsername(), $hash);
        $order = $this->requestBuilderCommon->buildOrder($request->getOrderId());

        $transaction = $this->requestBuilderCommon->buildTransaction(
            self::TRANSACTION_TYPE,
            $this->amountFormatter->format($request->getAmount()),
            $this->currencyCodeFormatter->format($request->getCurrency()),
            $this->installmentFormatter->format($request->getInstallment())
        );

        $baseRequest = $this->requestBuilderCommon->buildBaseRequest($terminal, $order, $transaction);
        $card = $this->requestBuilderCommon->buildCard($request->getCard());
        $data = array_merge($baseRequest, ['Card' => $card]);

        $xmlData = $this->serializer->serialize($data, ['root_name' => RequestBuilderCommon::ENVELOPE_NAME]);
        return http_build_query([RequestBuilderCommon::FORM_FIELD => $xmlData]);
    }
}
