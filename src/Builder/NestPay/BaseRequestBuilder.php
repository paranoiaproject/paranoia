<?php
namespace Paranoia\Builder\NestPay;

use Paranoia\Builder\AbstractRequestBuilder;
use Paranoia\Configuration\AbstractConfiguration;
use Paranoia\Formatter\DecimalFormatter;
use Paranoia\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Formatter\NestPay\ExpireDateFormatter;
use Paranoia\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Request\Resource\Card;
use Paranoia\Request\Resource\ResourceInterface;

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

    public function __construct(
        AbstractConfiguration $configuration,
        IsoNumericCurrencyCodeFormatter $currencyCodeFormatter,
        DecimalFormatter $amountFormatter,
        SingleDigitInstallmentFormatter $installmentFormatter,
        ExpireDateFormatter $expireDateFormatter
    ) {
        parent::__construct($configuration);
        $this->amountFormatter = $amountFormatter;
        $this->currencyCodeFormatter = $currencyCodeFormatter;
        $this->installmentFormatter = $installmentFormatter;
        $this->expireDateFormatter = $expireDateFormatter;
    }

    protected function buildBaseRequest($type)
    {
        /** @var \Paranoia\Configuration\NestpayConfiguration $config */
        $config = $this->configuration;
        return [
            'Mode'     => $config->getMode(),
            'ClientId' => $config->getClientId(),
            'Name'     => $config->getUsername(),
            'Password' => $config->getPassword(),
            'Type'     =>  $type,
        ];
    }

    protected function buildCard(ResourceInterface $card)
    {
        assert($card instanceof Card);

        /** @var Card $_card */
        $_card = $card;

        $expireDate = $this->expireDateFormatter->format(
            [
                $_card->getExpireMonth(),
                $_card->getExpireYear()
            ]
        );

        return array(
            'Number'     => $_card->getNumber(),
            'Cvv2Val'    => $_card->getSecurityCode(),
            'Expires'    => $expireDate
        );
    }
}
