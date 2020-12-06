<?php
namespace Paranoia\Acquirer\Gvp\RequestBuilder;

use Paranoia\Acquirer\Gvp\GvpConfiguration;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Model\Request\CaptureRequest;
use Paranoia\Core\Model\Request\HttpRequest;
use Paranoia\Lib\XmlSerializer;

/**
 * Class CaptureRequestBuilder
 * @package Paranoia\Acquirer\Gvp\RequestBuilder
 */
class CaptureRequestBuilder
{
    private const TRANSACTION_TYPE = 'postauth';

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

    /**
     * CaptureRequestBuilder constructor.
     * @param GvpConfiguration $configuration
     * @param RequestBuilderCommon $requestBuilderCommon
     * @param XmlSerializer $serializer
     * @param MoneyFormatter $amountFormatter
     * @param IsoNumericCurrencyCodeFormatter $currencyCodeFormatter
     */
    public function __construct(
        GvpConfiguration $configuration,
        RequestBuilderCommon $requestBuilderCommon,
        XmlSerializer $serializer,
        MoneyFormatter $amountFormatter,
        IsoNumericCurrencyCodeFormatter $currencyCodeFormatter
    ) {
        $this->configuration = $configuration;
        $this->requestBuilderCommon = $requestBuilderCommon;
        $this->serializer = $serializer;
        $this->amountFormatter = $amountFormatter;
        $this->currencyCodeFormatter = $currencyCodeFormatter;
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
        $hash = $this->requestBuilderCommon->buildHAshWithoutCard(
            $request->getOrderId(),
            $this->amountFormatter->format($request->getAmount()),
            $this->configuration->getAuthorizationPassword()
        );

        $terminal = $this->requestBuilderCommon->buildTerminal($this->configuration->getAuthorizationUsername(), $hash);
        $order = $this->requestBuilderCommon->buildOrder($request->getOrderId());

        $transaction = $this->requestBuilderCommon->buildTransaction(
            self::TRANSACTION_TYPE,
            $this->amountFormatter->format($request->getAmount()),
            $this->currencyCodeFormatter->format($request->getCurrency())
        );

        $baseRequest = $this->requestBuilderCommon->buildBaseRequest($terminal, $order, $transaction);

        $xmlData = $this->serializer->serialize($baseRequest, ['root_name' => RequestBuilderCommon::ENVELOPE_NAME]);
        return http_build_query([RequestBuilderCommon::FORM_FIELD => $xmlData]);
    }
}
