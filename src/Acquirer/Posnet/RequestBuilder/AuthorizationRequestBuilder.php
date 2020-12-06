<?php
namespace Paranoia\Acquirer\Posnet\RequestBuilder;

use Paranoia\Acquirer\Posnet\Formatter\CustomCurrencyCodeFormatter;
use Paranoia\Acquirer\Posnet\Formatter\OrderIdFormatter;
use Paranoia\Acquirer\Posnet\PosnetConfiguration;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\MultiDigitInstallmentFormatter;
use Paranoia\Core\Model\Request;
use Paranoia\Core\Model\Request\AuthorizationRequest;
use Paranoia\Core\Model\Request\HttpRequest;
use Paranoia\Lib\XmlSerializer;

/**
 * Class AuthorizationRequestBuilder
 * @package Paranoia\Acquirer\Posnet\RequestBuilder
 */
class AuthorizationRequestBuilder
{
    private const TRANSACTION_TYPE = 'auth';

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
     * AuthorizationRequestBuilder constructor.
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
     * @param AuthorizationRequest $request
     * @return HttpRequest
     */
    public function build(AuthorizationRequest $request): Request\HttpRequest
    {
        $headers = $this->requestBuilderCommon->buildHeaders();
        $body = $this->buildBody($request);

        return new HttpRequest($this->configuration->getApiUrl(), HttpRequest::HTTP_POST, $headers, $body);
    }

    /**
     * @param AuthorizationRequest $request
     * @return string
     */
    private function buildBody(AuthorizationRequest $request): string
    {
        $data = array_merge($this->requestBuilderCommon->buildBaseRequest(), $this->buildAuthorization($request));

        $xmlData = $this->serializer->serialize($data, ['root_name' => RequestBuilderCommon::ENVELOPE_NAME]);
        return http_build_query([RequestBuilderCommon::FORM_FIELD => $xmlData]);
    }

    /**
     * @param AuthorizationRequest $request
     * @return array
     */
    private function buildAuthorization(AuthorizationRequest $request): array
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
