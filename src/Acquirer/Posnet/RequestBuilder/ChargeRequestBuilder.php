<?php
namespace Paranoia\Acquirer\Posnet\RequestBuilder;

use Paranoia\Acquirer\Posnet\Formatter\CustomCurrencyCodeFormatter;
use Paranoia\Acquirer\Posnet\Formatter\OrderIdFormatter;
use Paranoia\Acquirer\Posnet\PosnetConfiguration;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\MultiDigitInstallmentFormatter;
use Paranoia\Core\Model\Request\ChargeRequest;
use Paranoia\Core\Model\Request\HttpRequest;
use Paranoia\Lib\XmlSerializer;

/**
 * Class ChargeRequestBuilder
 * @package Paranoia\Acquirer\Posnet\RequestBuilder
 */
class ChargeRequestBuilder
{
    private const TRANSACTION_TYPE = 'sale';

    /** @var PosnetConfiguration */
    private $configuration;

    /** @var RequestBuilderCommon */
    private $requestBuilderCommon;

    /** @var XmlSerializer */
    private $serializer;

    /** @var MoneyFormatter */
    protected $amountFormatter;

    /** @var  CustomCurrencyCodeFormatter */
    protected $currencyCodeFormatter;

    /** @var  MultiDigitInstallmentFormatter */
    protected $installmentFormatter;

    /** @var OrderIdFormatter */
    private $orderIdFormatter;

    /**
     * ChargeRequestBuilder constructor.
     * @param PosnetConfiguration $configuration
     * @param RequestBuilderCommon $requestBuilderCommon
     * @param XmlSerializer $serializer
     * @param MoneyFormatter $amountFormatter
     * @param CustomCurrencyCodeFormatter $currencyCodeFormatter
     * @param MultiDigitInstallmentFormatter $installmentFormatter
     * @param OrderIdFormatter $orderIdFormatter
     */
    public function __construct(
        PosnetConfiguration $configuration,
        RequestBuilderCommon $requestBuilderCommon,
        XmlSerializer $serializer,
        MoneyFormatter $amountFormatter,
        CustomCurrencyCodeFormatter $currencyCodeFormatter,
        MultiDigitInstallmentFormatter $installmentFormatter,
        OrderIdFormatter $orderIdFormatter
    ) {
        $this->configuration = $configuration;
        $this->requestBuilderCommon = $requestBuilderCommon;
        $this->serializer = $serializer;
        $this->amountFormatter = $amountFormatter;
        $this->currencyCodeFormatter = $currencyCodeFormatter;
        $this->installmentFormatter = $installmentFormatter;
        $this->orderIdFormatter = $orderIdFormatter;
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
        $data = array_merge($this->requestBuilderCommon->buildBaseRequest(), $this->buildCharge($request));

        $xmlData = $this->serializer->serialize($data, ['root_name' => RequestBuilderCommon::ENVELOPE_NAME]);
        return http_build_query([RequestBuilderCommon::FORM_FIELD => $xmlData]);
    }

    /**
     * @param ChargeRequest $request
     * @return array
     */
    private function buildCharge(ChargeRequest $request): array
    {
        $context = [
            'amount' => $this->amountFormatter->format($request->getAmount()),
            'currencyCode' => $this->currencyCodeFormatter->format($request->getCurrency()),
            'orderID' => $this->orderIdFormatter->format($request->getOrderId())
        ];

        if ($request->getInstallment()) {
            $context['installment'] = $this->installmentFormatter->format($request->getInstallment());
        }

        return [
            self::TRANSACTION_TYPE => array_merge(
                $context,
                $this->requestBuilderCommon->buildCard($request->getCard())
            )
        ];
    }
}
