<?php
namespace Paranoia\Builder\Posnet;

use Paranoia\Builder\AbstractRequestBuilder;
use Paranoia\Configuration\AbstractConfiguration;
use Paranoia\Configuration\Posnet;
use Paranoia\Formatter\MoneyFormatter;
use Paranoia\Formatter\MultiDigitInstallmentFormatter;
use Paranoia\Formatter\Posnet\CustomCurrencyCodeFormatter;
use Paranoia\Formatter\Posnet\ExpireDateFormatter;
use Paranoia\Formatter\Posnet\OrderIdFormatter;
use Paranoia\Request;

abstract class BaseRequestBuilder extends AbstractRequestBuilder
{
    /** @var DecimalFormatter */
    protected $amountFormatter;

    /** @var  IsoNumericCurrencyCodeFormatter */
    protected $currencyCodeFormatter;

    /** @var  SingleDigitInstallmentFormatter */
    protected $installmentFormatter;

    /** @var  ExpireDateFormatter */
    protected $expireDateFormatter;

    /** @var OrderIdFormatter OrderId */
    protected $orderIdFormatter;

    public function __construct(
        AbstractConfiguration $configuration,
        CustomCurrencyCodeFormatter $currencyCodeFormatter,
        MoneyFormatter $amountFormatter,
        MultiDigitInstallmentFormatter $installmentFormatter,
        ExpireDateFormatter $expireDateFormatter,
        OrderIdFormatter $orderIdFormatter
    ) {
        parent::__construct($configuration);
        $this->currencyCodeFormatter = $currencyCodeFormatter;
        $this->amountFormatter = $amountFormatter;
        $this->installmentFormatter = $installmentFormatter;
        $this->expireDateFormatter = $expireDateFormatter;
        $this->orderIdFormatter = $orderIdFormatter;
    }

    protected function buildBaseRequest(Request $request)
    {
        /** @var Posnet $configuration */
        $configuration = $this->configuration;
        return [
            'mid' => $configuration->getMerchantId(),
            'tid' => $configuration->getTerminalId(),
            'username' => $configuration->getUsername(),
            'password' => $configuration->getPassword()
        ];
    }

    protected function buildCard(Request $request)
    {
        return [
            'ccno' => $request->getCardNumber(),
            'cvc' => $request->getSecurityCode(),
            'expDate' => $this->expireDateFormatter->format(
                [
                    $request->getExpireMonth(),
                    $request->getExpireYear()
                ]
            )
        ];
    }
}
