<?php
namespace Paranoia\Nestpay\RequestBuilder;

use Paranoia\Configuration\NestpayConfiguration;
use Paranoia\Core\Formatter\DecimalFormatter;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Core\Request\ChargeRequest;
use Paranoia\Core\RequestBuilder\ChargeRequestBuilder as CoreChargeRequestBuilderAlias;
use Paranoia\Core\Serializer\Serializer;
use Paranoia\Nestpay\Formatter\ExpireDateFormatter;

class ChargeRequestBuilder implements CoreChargeRequestBuilderAlias
{
    const TRANSACTION_TYPE = 'Auth';
    const ENVELOPE_NAME = 'CC5Request';

    /** @var NestpayConfiguration */
    protected $configuration;

    /** @var DecimalFormatter */
    protected $amountFormatter;

    /** @var IsoNumericCurrencyCodeFormatter */
    protected $currencyFormatter;

    /** @var ExpireDateFormatter */
    protected $expireDateFormatter;

    /** @var SingleDigitInstallmentFormatter */
    protected $installmentFormatter;

    /**
     * AuthorizationRequestBuilder constructor.
     * @param NestpayConfiguration $configuration
     * @param DecimalFormatter $amountFormatter
     * @param IsoNumericCurrencyCodeFormatter $currencyFormatter
     * @param ExpireDateFormatter $expireDateFormatter
     * @param SingleDigitInstallmentFormatter $installmentFormatter
     */
    public function __construct(
        NestpayConfiguration $configuration,
        DecimalFormatter $amountFormatter,
        IsoNumericCurrencyCodeFormatter $currencyFormatter,
        ExpireDateFormatter $expireDateFormatter,
        SingleDigitInstallmentFormatter $installmentFormatter
    ) {
        $this->configuration = $configuration;
        $this->amountFormatter = $amountFormatter;
        $this->currencyFormatter = $currencyFormatter;
        $this->expireDateFormatter = $expireDateFormatter;
        $this->installmentFormatter = $installmentFormatter;
    }
    public function build(ChargeRequest $request): array
    {
        $data = [
            'Name' => $this->configuration->getUsername(),
            'ClientId' => $this->configuration->getClientId(),
            'Type' => self::TRANSACTION_TYPE,
            'OrderId' => $request->getOrderId(),
            'Total' => $this->amountFormatter->format($request->getAmount()),
            'Currency' => $this->currencyFormatter->format($request->getCurrency()),
            'Number' => $request->getCardNumber(),
            'Expires' => $this->expireDateFormatter->format($request->getCardExpireMonth(), $request->getCardExpireYear()),
            'Cvv2Val' => $request->getCardCvv()
        ];

        $formattedInstallment = $this->installmentFormatter->format($request->getInstallment());
        if ($formattedInstallment) {
            $data['Taksit'] = $formattedInstallment;
        }

        $serializer = new Serializer(Serializer::XML);
        $xml =  $serializer->serialize($data, ['root_name' => self::ENVELOPE_NAME]);
        return ['DATA' => $xml];
    }
}
