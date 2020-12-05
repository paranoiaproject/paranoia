<?php
namespace Paranoia\Acquirer\Posnet\RequestBuilder;

use Paranoia\Acquirer\AbstractRequestBuilder;
use Paranoia\Acquirer\Posnet\Formatter\CustomCurrencyCodeFormatter;
use Paranoia\Acquirer\Posnet\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\Posnet\Formatter\OrderIdFormatter;
use Paranoia\Acquirer\Posnet\PosnetConfiguration;
use Paranoia\Core\AbstractConfiguration;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\MultiDigitInstallmentFormatter;
use Paranoia\Core\Model\Request;
use Paranoia\Core\Model\Request\Resource\Card;
use Paranoia\Core\Model\Request\Resource\ResourceInterface;

abstract class BaseRequestBuilder extends AbstractRequestBuilder
{
    /** @var MoneyFormatter */
    protected $amountFormatter;

    /** @var  CustomCurrencyCodeFormatter */
    protected $currencyCodeFormatter;

    /** @var  MultiDigitInstallmentFormatter */
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
        /** @var PosnetConfiguration $configuration */
        $configuration = $this->configuration;
        return [
            'mid' => $configuration->getMerchantId(),
            'tid' => $configuration->getTerminalId(),
            'username' => $configuration->getUsername(),
            'password' => $configuration->getPassword()
        ];
    }

    protected function buildCard(ResourceInterface $card)
    {
        assert($card instanceof Card);

        /** @var Card $_card */
        $_card = $card;

        return [
            'ccno' => $_card->getNumber(),
            'cvc' => $_card->getSecurityCode(),
            'expDate' => $this->expireDateFormatter->format(
                [
                    $_card->getExpireMonth(),
                    $_card->getExpireYear()
                ]
            )
        ];
    }
}
