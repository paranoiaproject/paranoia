<?php
namespace Paranoia\Posnet\RequestBuilder;

use Paranoia\Configuration\PosnetConfiguration;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\MultiDigitInstallmentFormatter;
use Paranoia\Core\Request\ChargeRequest;
use Paranoia\Core\RequestBuilder\ChargeRequestBuilder as CoreChargeRequestBuilderAlias;
use Paranoia\Core\Serializer\Serializer;
use Paranoia\Posnet\Formatter\CustomCurrencyCodeFormatter;
use Paranoia\Posnet\Formatter\ExpireDateFormatter;
use Paranoia\Posnet\Formatter\OrderIdFormatter;

class ChargeRequestBuilder implements CoreChargeRequestBuilderAlias
{
    const TRANSACTION_TYPE = 'sale';
    const ENVELOPE_NAME    = 'posnetRequest';
    const FORM_FIELD = 'xmldata';

    /** @var PosnetConfiguration */
    protected $configuration;

    /** @var MoneyFormatter */
    protected $amountFormatter;

    /** @var CustomCurrencyCodeFormatter */
    protected $currencyFormatter;

    /** @var MultiDigitInstallmentFormatter */
    protected $installmentFormatter;

    /** @var ExpireDateFormatter */
    protected $expireDateFormatter;

    /** @var OrderIdFormatter */
    protected $orderIdFormatter;

    /**
     * ChargeRequestBuilder constructor.
     * @param PosnetConfiguration $configuration
     * @param MoneyFormatter $amountFormatter
     * @param CustomCurrencyCodeFormatter $currencyFormatter
     * @param ExpireDateFormatter $expireDateFormatter
     * @param MultiDigitInstallmentFormatter $installmentFormatter
     * @param OrderIdFormatter $orderIdFormatter
     */
    public function __construct(
        PosnetConfiguration $configuration,
        MoneyFormatter $amountFormatter,
        CustomCurrencyCodeFormatter $currencyFormatter,
        ExpireDateFormatter $expireDateFormatter,
        MultiDigitInstallmentFormatter $installmentFormatter,
        OrderIdFormatter $orderIdFormatter
    ) {
        $this->configuration = $configuration;
        $this->amountFormatter = $amountFormatter;
        $this->currencyFormatter = $currencyFormatter;
        $this->expireDateFormatter = $expireDateFormatter;
        $this->installmentFormatter = $installmentFormatter;
        $this->orderIdFormatter = $orderIdFormatter;
    }

    public function build(ChargeRequest $request): array
    {
        $data = [
            'mid' => $this->configuration->getMerchantId(),
            'tid' => $this->configuration->getTerminalId(),
            'username' => $this->configuration->getUsername(),
            'password' => $this->configuration->getPassword(),
            self::TRANSACTION_TYPE => [
                'amount' => $this->amountFormatter->format($request->getAmount()),
                'currencyCode' => $this->currencyFormatter->format($request->getCurrency()),
                'orderID' => $this->orderIdFormatter->format($request->getOrderId()),
                'ccno' => $request->getCardNumber(),
                'cvc' => $request->getCardCvv(),
                'expDate' => $this->expireDateFormatter->format(
                    $request->getCardExpireMonth(),
                    $request->getCardExpireYear()
                )
            ]
        ];

        if ($request->getInstallment() && $request->getInstallment() > 1) {
            $formattedInstallment = $this->installmentFormatter->format($request->getInstallment());
            $data[self::TRANSACTION_TYPE]['installment'] = $formattedInstallment;
        }

        $serializer = new Serializer(Serializer::XML);
        $xml =  $serializer->serialize($data, ['root_name' => self::ENVELOPE_NAME]);
        return [self::FORM_FIELD => $xml];
    }
}
