<?php
namespace Paranoia\Acquirer\Posnet\RequestBuilder;

use Paranoia\Acquirer\Posnet\Formatter\CustomCurrencyCodeFormatter;
use Paranoia\Acquirer\Posnet\PosnetConfiguration;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\MultiDigitInstallmentFormatter;
use Paranoia\Core\Model\Request\CaptureRequest;
use Paranoia\Core\Model\Request\HttpRequest;
use Paranoia\Lib\XmlSerializer;

/**
 * Class CaptureRequestBuilder
 * @package Paranoia\Acquirer\Posnet\RequestBuilder
 */
class CaptureRequestBuilder
{
    private const TRANSACTION_TYPE = 'capt';

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

    /**
     * CaptureRequestBuilder constructor.
     * @param PosnetConfiguration $configuration
     * @param RequestBuilderCommon $requestBuilderCommon
     * @param XmlSerializer $serializer
     * @param MoneyFormatter $amountFormatter
     * @param CustomCurrencyCodeFormatter $currencyCodeFormatter
     * @param MultiDigitInstallmentFormatter $installmentFormatter
     */
    public function __construct(
        PosnetConfiguration $configuration,
        RequestBuilderCommon $requestBuilderCommon,
        XmlSerializer $serializer,
        MoneyFormatter $amountFormatter,
        CustomCurrencyCodeFormatter $currencyCodeFormatter,
        MultiDigitInstallmentFormatter $installmentFormatter
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
    private function buildBody(CaptureRequest $request): string
    {
        $data = array_merge($this->requestBuilderCommon->buildBaseRequest(), $this->buildCapture($request));

        if ($request->getInstallment()) {
            $data[self::TRANSACTION_TYPE]['installment'] = $this->installmentFormatter->format(
                $request->getInstallment()
            );
        }

        $xmlData = $this->serializer->serialize($data, ['root_name' => RequestBuilderCommon::ENVELOPE_NAME]);
        return http_build_query([RequestBuilderCommon::FORM_FIELD => $xmlData]);
    }

    /**
     * @param CaptureRequest $request
     * @return array
     */
    private function buildCapture(CaptureRequest $request): array
    {
        $context = [
            'amount' => $this->amountFormatter->format($request->getAmount()),
            'currencyCode' => $this->currencyCodeFormatter->format($request->getCurrency()),
            'hostLogKey' => $request->getTransactionId()
        ];

        if ($request->getInstallment()) {
            $context['installment'] = $this->installmentFormatter->format($request->getInstallment());
        }

        return [
            self::TRANSACTION_TYPE => $context
        ];
    }
}
