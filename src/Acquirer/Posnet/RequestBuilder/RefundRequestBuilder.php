<?php
namespace Paranoia\Acquirer\Posnet\RequestBuilder;

use Paranoia\Acquirer\Posnet\Formatter\CustomCurrencyCodeFormatter;
use Paranoia\Acquirer\Posnet\PosnetConfiguration;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Model\Request\HttpRequest;
use Paranoia\Core\Model\Request\RefundRequest;
use Paranoia\Lib\XmlSerializer;

/**
 * Class RefundRequestBuilder
 * @package Paranoia\Acquirer\Posnet\RequestBuilder
 */
class RefundRequestBuilder
{
    private const TRANSACTION_TYPE = 'return';

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

    /**
     * RefundRequestBuilder constructor.
     * @param PosnetConfiguration $configuration
     * @param RequestBuilderCommon $requestBuilderCommon
     * @param XmlSerializer $serializer
     * @param MoneyFormatter $amountFormatter
     * @param CustomCurrencyCodeFormatter $currencyCodeFormatter
     */
    public function __construct(
        PosnetConfiguration $configuration,
        RequestBuilderCommon $requestBuilderCommon,
        XmlSerializer $serializer,
        MoneyFormatter $amountFormatter,
        CustomCurrencyCodeFormatter $currencyCodeFormatter
    ) {
        $this->configuration = $configuration;
        $this->requestBuilderCommon = $requestBuilderCommon;
        $this->serializer = $serializer;
        $this->amountFormatter = $amountFormatter;
        $this->currencyCodeFormatter = $currencyCodeFormatter;
    }

    /**
     * @param RefundRequest $request
     * @return HttpRequest
     */
    public function build(RefundRequest $request): HttpRequest
    {
        $headers = $this->requestBuilderCommon->buildHeaders();
        $body = $this->buildBody($request);

        return new HttpRequest($this->configuration->getApiUrl(), HttpRequest::HTTP_POST, $headers, $body);
    }

    /**
     * @param RefundRequest $request
     * @return string
     */
    private function buildBody(RefundRequest $request): string
    {
        $data = array_merge($this->requestBuilderCommon->buildBaseRequest(), $this->buildRefund($request));

        $xmlData = $this->serializer->serialize($data, ['root_name' => RequestBuilderCommon::ENVELOPE_NAME]);
        return http_build_query([RequestBuilderCommon::FORM_FIELD => $xmlData]);
    }

    /**
     * @param RefundRequest $request
     * @return array
     */
    private function buildRefund(RefundRequest $request): array
    {
        return [
            self::TRANSACTION_TYPE => [
                'amount' => $this->amountFormatter->format($request->getAmount()),
                'currencyCode' => $this->currencyCodeFormatter->format($request->getCurrency()),
                'hostLogKey' => $request->getTransactionId()
            ]
        ];
    }
}
