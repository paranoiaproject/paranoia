<?php
namespace Paranoia\Acquirer\NestPay\RequestBuilder;

use Paranoia\Acquirer\NestPay\NestPayConfiguration;
use Paranoia\Core\Formatter\DecimalFormatter;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Core\Model\Request\CaptureRequest;
use Paranoia\Core\Model\Request\HttpRequest;
use Paranoia\Lib\XmlSerializer;

/**
 * Class CaptureRequestBuilder
 * @package Paranoia\Acquirer\NestPay\RequestBuilder
 */
class CaptureRequestBuilder
{
    private const TRANSACTION_TYPE = 'PostAuth';

    /** @var NestPayConfiguration */
    private $configuration;

    /** @var RequestBuilderCommon */
    private $requestBuilderCommon;

    /** @var XmlSerializer */
    private $serializer;

    /** @var DecimalFormatter */
    private $amountFormatter;

    /** @var  IsoNumericCurrencyCodeFormatter */
    private $currencyCodeFormatter;

    /** @var  SingleDigitInstallmentFormatter */
    private $installmentFormatter;

    /**
     * CaptureRequestBuilder constructor.
     * @param NestPayConfiguration $configuration
     * @param RequestBuilderCommon $requestBuilderCommon
     * @param XmlSerializer $serializer
     * @param DecimalFormatter $amountFormatter
     * @param IsoNumericCurrencyCodeFormatter $currencyCodeFormatter
     * @param SingleDigitInstallmentFormatter $installmentFormatter
     */
    public function __construct(
        NestPayConfiguration $configuration,
        RequestBuilderCommon $requestBuilderCommon,
        XmlSerializer $serializer,
        DecimalFormatter $amountFormatter,
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
     * @param CaptureRequest $request
     * @return HttpRequest
     */
    public function build(CaptureRequest $request): HttpRequest
    {
        $headers = $this->requestBuilderCommon->buildHeaders();
        $body = $this->buildBody($request);

        return new HttpRequest($this->configuration->getApiUrl(), HttpRequest::HTTP_POST, $headers, $body);
    }

    /**
     * @param CaptureRequest $request
     * @return string
     */
    private function buildBody(CaptureRequest $request)
    {
        $data = array_merge($this->requestBuilderCommon->buildBaseRequest(self::TRANSACTION_TYPE), [
            'OrderId' => $request->getOrderId(),
            'Total' => $this->amountFormatter->format($request->getAmount()),
            'Currency' => $this->currencyCodeFormatter->format($request->getCurrency()),
        ]);

        if ($request->getInstallment()) {
            $data['Taksit'] = $this->installmentFormatter->format($request->getInstallment());
        }

        $xmlData = $this->serializer->serialize($data, ['root_name' => RequestBuilderCommon::ENVELOPE_NAME]);
        return http_build_query([RequestBuilderCommon::FORM_FIELD => $xmlData]);
    }
}
