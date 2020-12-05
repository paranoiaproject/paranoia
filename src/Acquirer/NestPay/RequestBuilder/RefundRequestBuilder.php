<?php
namespace Paranoia\Acquirer\NestPay\RequestBuilder;

use Paranoia\Acquirer\NestPay\NestPayConfiguration;
use Paranoia\Core\Formatter\DecimalFormatter;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Model\Request\HttpRequest;
use Paranoia\Core\Model\Request\RefundRequest;
use Paranoia\Lib\XmlSerializer;

/**
 * Class RefundRequestBuilder
 * @package Paranoia\Acquirer\NestPay\RequestBuilder
 */
class RefundRequestBuilder
{
    private const TRANSACTION_TYPE = 'Credit';

    /** @var NestPayConfiguration */
    private $configuration;

    /** @var RequestBuilderCommon */
    private $requestBuilderCommon;

    /** @var XmlSerializer */
    protected $serializer;

    /** @var DecimalFormatter */
    protected $amountFormatter;

    /** @var  IsoNumericCurrencyCodeFormatter */
    protected $currencyCodeFormatter;

    /**
     * RefundRequestBuilder constructor.
     * @param NestPayConfiguration $configuration
     * @param RequestBuilderCommon $requestBuilderCommon
     * @param XmlSerializer $serializer
     * @param DecimalFormatter $amountFormatter
     * @param IsoNumericCurrencyCodeFormatter $currencyCodeFormatter
     */
    public function __construct(
        NestPayConfiguration $configuration,
        RequestBuilderCommon $requestBuilderCommon,
        XmlSerializer $serializer,
        DecimalFormatter $amountFormatter,
        IsoNumericCurrencyCodeFormatter $currencyCodeFormatter
    ) {
        $this->configuration = $configuration;
        $this->requestBuilderCommon = $requestBuilderCommon;
        $this->serializer = $serializer;
        $this->amountFormatter = $amountFormatter;
        $this->currencyCodeFormatter = $currencyCodeFormatter;
    }

    /**
     * @param RefundRequest $request
     * @return string
     */
    private function buildBody(RefundRequest $request): string
    {
        $data = array_merge(
            $this->requestBuilderCommon->buildBaseRequest(self::TRANSACTION_TYPE),
            [
                'OrderId'  => $request->getOrderId(),
            ]
        );

        if ($request->getAmount()) {
            $data = array_merge($data, [
                'Total'    => $this->amountFormatter->format($request->getAmount()),
                'Currency' => $this->currencyCodeFormatter->format($request->getCurrency()),
            ]);
        }

        $xmlData = $this->serializer->serialize($data, ['root_name' => RequestBuilderCommon::ENVELOPE_NAME]);
        return http_build_query([RequestBuilderCommon::FORM_FIELD => $xmlData]);
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
}
