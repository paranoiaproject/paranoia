<?php
namespace Paranoia\Acquirer\Gvp\RequestBuilder;

use Paranoia\Acquirer\AbstractRequestBuilder;
use Paranoia\Acquirer\Gvp\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\Gvp\GvpConfiguration as GvpConfiguration;
use Paranoia\Core\AbstractConfiguration;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Core\Model\Request;
use Paranoia\Core\Model\Request\Resource\Card;
use Paranoia\Core\Model\Request\Resource\ResourceInterface;

abstract class BaseRequestBuilder extends AbstractRequestBuilder
{
    const API_VERSION = '0.01';
    const CARD_HOLDER_PRESENT_CODE_NON_3D = 0;
    const CARD_HOLDER_PRESENT_CODE_3D = 13;

    /** @var MoneyFormatter */
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
        MoneyFormatter $amountFormatter,
        SingleDigitInstallmentFormatter $installmentFormatter,
        ExpireDateFormatter $expireDateFormatter
    ) {
        parent::__construct($configuration);
        $this->currencyCodeFormatter = $currencyCodeFormatter;
        $this->amountFormatter = $amountFormatter;
        $this->installmentFormatter = $installmentFormatter;
        $this->expireDateFormatter = $expireDateFormatter;
    }

    protected function buildBaseRequest(Request $request)
    {
        /** @var GvpConfiguration $configuration */
        $configuration = $this->configuration;
        return [
            'Version' => self::API_VERSION,
            'Mode' => $configuration->getMode(),
            'Terminal' => $this->buildTerminal($request),
            'Order' => $this->buildOrder($request),
            'Customer' => $this->buildCustomer(),
            'Transaction'  => $this->buildTransaction($request)
        ];
    }

    abstract protected function buildTransaction(Request $request);
    abstract protected function getCredentialPair();
    abstract protected function buildHash(Request $request, $password);

    protected function buildCustomer()
    {
        return [
            'IPAddress'    => '127.0.0.1',
            'EmailAddress' => 'dummy@dummy.net'
        ];
    }

    protected function generateSecurityHash($password)
    {
        /** @var GvpConfiguration $configuration */
        $configuration = $this->configuration;

        $tidPrefix  = str_repeat('0', 9 - strlen($configuration->getTerminalId()));
        $terminalId = sprintf('%s%s', $tidPrefix, $configuration->getTerminalId());
        return strtoupper(SHA1(sprintf('%s%s', $password, $terminalId)));
    }

    protected function buildTerminal(Request $request)
    {
        /** @var GvpConfiguration $configuration */
        $configuration = $this->configuration;

        list($username, $password) = $this->getCredentialPair();

        $hash = $this->buildHash($request, $password);

        return array(
            'ProvUserID' => $username,
            'HashData'   => $hash,
            'UserID'     => $username,
            'ID'         => $configuration->getTerminalId(),
            'MerchantID' => $configuration->getMerchantId()
        );
    }

    protected function buildCard(ResourceInterface $card)
    {
        assert($card instanceof Card);

        /** @var Card $_card */
        $_card = $card;

        $expireMonth = $this->expireDateFormatter->format(
            [
                $_card->getExpireMonth(),
                $_card->getExpireYear()
            ]
        );

        return array(
            'Number'     => $_card->getNumber(),
            'ExpireDate' => $expireMonth,
            'CVV2'       => $_card->getSecurityCode()
        );
    }

    protected function buildOrder(Request $request)
    {
        return [
            'OrderID'     => $request->getOrderId(),
            'GroupID'     => null,
            'Description' => null
        ];
    }
}
