<?php
namespace Paranoia\Gvp\RequestBuilder;

use Paranoia\Configuration\GvpConfiguration;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Core\Request\ChargeRequest;
use Paranoia\Core\RequestBuilder\ChargeRequestBuilder as CoreChargeRequestBuilderAlias;
use Paranoia\Core\Serializer\Serializer;
use Paranoia\Gvp\Formatter\ExpireDateFormatter;

class ChargeRequestBuilder extends BaseRequestBuilder implements CoreChargeRequestBuilderAlias
{
    const TRANSACTION_TYPE = 'sales';
    const ENVELOPE_NAME = 'GVPSRequest';
    const API_VERSION = 'v0.01';
    const CARD_HOLDER_PRESENT_CODE_NON_3D = 0;
    const FORM_FIELD = 'data';

    /** @var MoneyFormatter */
    protected $amountFormatter;

    /** @var IsoNumericCurrencyCodeFormatter */
    protected $currencyFormatter;

    /** @var ExpireDateFormatter */
    protected $expireDateFormatter;

    /** @var SingleDigitInstallmentFormatter */
    protected $installmentFormatter;

    /**
     * ChargeRequestBuilder constructor.
     * @param GvpConfiguration $configuration
     * @param MoneyFormatter $amountFormatter
     * @param IsoNumericCurrencyCodeFormatter $currencyFormatter
     * @param ExpireDateFormatter $expireDateFormatter
     * @param SingleDigitInstallmentFormatter $installmentFormatter
     */
    public function __construct(
        GvpConfiguration $configuration,
        MoneyFormatter $amountFormatter,
        IsoNumericCurrencyCodeFormatter $currencyFormatter,
        ExpireDateFormatter $expireDateFormatter,
        SingleDigitInstallmentFormatter $installmentFormatter
    ) {
        parent::__construct($configuration);
        $this->amountFormatter = $amountFormatter;
        $this->currencyFormatter = $currencyFormatter;
        $this->expireDateFormatter = $expireDateFormatter;
        $this->installmentFormatter = $installmentFormatter;
    }

    /**
     * @param ChargeRequest $request
     * @return array
     */
    public function build(ChargeRequest $request): array
    {
        $hash = $this->buildHash(
            [
                $request->getOrderId(),
                $this->configuration->getTerminalId(),
                $request->getCardNumber(),
                $this->amountFormatter->format($request->getAmount()),
            ],
            $this->configuration->getAuthorizationPassword()
        );

        $data = [
            'Version' => self::API_VERSION,
            'Mode' => $this->configuration->getMode(),
            'Terminal' => $this->buildTerminal($this->configuration->getAuthorizationUsername(), $hash),
            'Order' => $this->buildOrder($request->getOrderId()),
            'Customer' => $this->buildCustomer(),
            'Transaction' => $this->buildTransaction(
                $request->getAmount(),
                $request->getCurrency(),
                $request->getInstallment()
            ),
            'Card' => $this->buildCard(
                $request->getCardNumber(),
                $request->getCardCvv(),
                $request->getCardExpireMonth(),
                $request->getCardExpireYear()
            )
        ];

        $serializer = new Serializer(Serializer::XML);
        $xml =  $serializer->serialize($data, ['root_name' => self::ENVELOPE_NAME]);
        return [self::FORM_FIELD => $xml];
    }

    /**
     * @param float $amount
     * @param string $currency
     * @param int|null $installment
     * @return array
     */
    private function buildTransaction(float $amount, string $currency, ?int $installment): array
    {
        $data = [
            'Type' => self::TRANSACTION_TYPE,
            'Amount' => $this->amountFormatter->format($amount),
            'CurrencyCode' => $this->currencyFormatter->format($currency),
            'CardholderPresentCode' => self::CARD_HOLDER_PRESENT_CODE_NON_3D,
            'MotoInd' => 'N',
            'OriginalRetrefNum' => null,
        ];

        $formattedInstallment = $this->installmentFormatter->format($installment);

        if ($formattedInstallment) {
            $data['InstallmentCnt'] = $formattedInstallment;
        }

        return $data;
    }

    /**
     * @param string $cardNumber
     * @param string $cvv
     * @param int $cardExpireMonth
     * @param int $cardExpireYear
     * @return array
     */
    private function buildCard(string $cardNumber, string $cvv, int $cardExpireMonth, int $cardExpireYear): array
    {
        return [
            'Number' => $cardNumber,
            'ExpireDate' => $this->expireDateFormatter->format(
                $cardExpireMonth,
                $cardExpireYear
            ),
            'CVV2' => $cvv
        ];
    }
}
