<?php
namespace Paranoia\Gvp\RequestBuilder;

use Paranoia\Configuration\GvpConfiguration;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Request\CaptureRequest;
use Paranoia\Core\RequestBuilder\CaptureRequestBuilder as CoreCaptureRequestBuilderAlias;
use Paranoia\Core\Serializer\Serializer;

class CaptureRequestBuilder extends BaseRequestBuilder implements CoreCaptureRequestBuilderAlias
{
    const TRANSACTION_TYPE = 'postauth';
    const ENVELOPE_NAME = 'GVPSRequest';
    const API_VERSION = 'v0.01';
    const CARD_HOLDER_PRESENT_CODE_NON_3D = 0;
    const FORM_FIELD = 'data';

    /** @var MoneyFormatter */
    protected $amountFormatter;

    /** @var IsoNumericCurrencyCodeFormatter */
    protected $currencyFormatter;

    /**
     * CaptureRequestBuilder constructor.
     * @param GvpConfiguration $configuration
     * @param MoneyFormatter $amountFormatter
     * @param IsoNumericCurrencyCodeFormatter $currencyFormatter
     */
    public function __construct(
        GvpConfiguration $configuration,
        MoneyFormatter $amountFormatter,
        IsoNumericCurrencyCodeFormatter $currencyFormatter
    ) {
        parent::__construct($configuration);
        $this->amountFormatter = $amountFormatter;
        $this->currencyFormatter = $currencyFormatter;
    }

    /**
     * @param CaptureRequest $request
     * @return array
     */
    public function build(CaptureRequest $request): array
    {
        $hash = $this->buildHash(
            [
                $request->getTransactionRef(),
                $this->configuration->getTerminalId(),
                $this->amountFormatter->format($request->getAmount()),
            ],
            $this->configuration->getAuthorizationPassword()
        );

        $data = [
            'Version' => self::API_VERSION,
            'Mode' => $this->configuration->getMode(),
            'Terminal' => $this->buildTerminal($this->configuration->getAuthorizationUsername(), $hash),
            'Order' => $this->buildOrder($request->getTransactionRef()),
            'Customer' => $this->buildCustomer(),
            'Transaction' => $this->buildTransaction(
                $request->getAmount(),
                $request->getCurrency()
            ),
        ];

        $serializer = new Serializer(Serializer::XML);
        $xml =  $serializer->serialize($data, ['root_name' => self::ENVELOPE_NAME]);
        return [self::FORM_FIELD => $xml];
    }

    /**
     * @param float $amount
     * @param string $currency
     * @return array
     */
    private function buildTransaction(float $amount, string $currency): array
    {
        $data = [
            'Type' => self::TRANSACTION_TYPE,
            'Amount' => $this->amountFormatter->format($amount),
            'CurrencyCode' => $this->currencyFormatter->format($currency),
            'CardholderPresentCode' => self::CARD_HOLDER_PRESENT_CODE_NON_3D,
            'MotoInd' => 'N',
            'OriginalRetrefNum' => null,
        ];

        return $data;
    }
}
