<?php
namespace Paranoia\Acquirer\Gvp\RequestBuilder;

use Paranoia\Acquirer\Gvp\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\Gvp\GvpConfiguration;
use Paranoia\Core\Model\Request\Card;

/**
 * Class RequestBuilderCommon
 * @package Paranoia\Acquirer\Gvp\RequestBuilder
 */
class RequestBuilderCommon
{
    public const ENVELOPE_NAME = 'GVPSRequest';
    public const FORM_FIELD = 'data';
    private const API_VERSION = '0.01';
    private const CARD_HOLDER_PRESENT_CODE_NON_3D = 0;
    private const CARD_HOLDER_PRESENT_CODE_3D = 13;

    /** @var GvpConfiguration */
    private $configuration;

    /** @var  ExpireDateFormatter */
    protected $expireDateFormatter;

    /**
     * RequestBuilderCommon constructor.
     * @param GvpConfiguration $configuration
     * @param ExpireDateFormatter $expireDateFormatter
     */
    public function __construct(
        GvpConfiguration $configuration,
        ExpireDateFormatter $expireDateFormatter
    ) {
        $this->configuration = $configuration;
        $this->expireDateFormatter = $expireDateFormatter;
    }

    /**
     * @param array $terminal
     * @param array $order
     * @param array $transaction
     * @return array
     */
    public function buildBaseRequest(array $terminal, array $order, array $transaction): array
    {
        return [
            'Version' => self::API_VERSION,
            'Mode' => $this->configuration->getMode(),
            'Terminal' => $terminal,
            'Order' => $order,
            'Customer' => $this->buildCustomer(),
            'Transaction'  => $transaction
        ];
    }

    /**
     * @param Card $card
     * @return array
     */
    public function buildCard(Card $card): array
    {
        $expireMonth = $this->expireDateFormatter->format([$card->getExpireMonth(), $card->getExpireYear()]);

        return array(
            'Number'     => $card->getNumber(),
            'ExpireDate' => $expireMonth,
            'CVV2'       => $card->getCvv()
        );
    }

    /**
     * @param string $username
     * @param string $hash
     * @return array
     */
    public function buildTerminal(string $username, string $hash): array
    {
        return array(
            'ProvUserID' => $username,
            'HashData'   => $hash,
            'UserID'     => $username,
            'ID'         => $this->configuration->getTerminalId(),
            'MerchantID' => $this->configuration->getMerchantId()
        );
    }

    /**
     * @param string $orderId
     * @return array
     */
    public function buildOrder(string $orderId): array
    {
        return [
            'OrderID'     => $orderId,
            'GroupID'     => null,
            'Description' => null
        ];
    }

    /**
     * @param string $transactionType
     * @param float $formattedAmount
     * @param string $formattedCurrency
     * @param string|null $formattedInstallment
     * @return array
     */
    public function buildTransaction(
        string $transactionType,
        string $formattedAmount,
        string $formattedCurrency,
        string $formattedInstallment = null
    ): array {
        $transaction = [
            'Amount' => $formattedAmount,
            'CurrencyCode' => $formattedCurrency,

            #TODO: Will be changed after 3D integration
            'CardholderPresentCode' => self::CARD_HOLDER_PRESENT_CODE_NON_3D,

            'MotoInd' => 'N',
            'OriginalRetrefNum' => null,
        ];

        if ($formattedInstallment !== null) {
            $transaction = array_merge(['InstallmentCnt' => $formattedInstallment], $transaction);
        }

        return array_merge(['Type' => $transactionType], $transaction);
    }

    /**
     * @return array
     */
    public function buildHeaders(): array
    {
        return [
            'Content-Type' => 'application x/www-form-data',
        ];
    }

    /**
     * @param Card $card
     * @param string $orderId
     * @param string $formattedAmount
     * @param $password
     * @return string
     */
    public function buildHashWithCard(Card $card, string $orderId, string $formattedAmount, $password): string
    {
        return strtoupper(
            sha1(
                sprintf(
                    '%s%s%s%s%s',
                    $orderId,
                    $this->configuration->getTerminalId(),
                    $card->getNumber(),
                    $formattedAmount,
                    $this->generateSecurityHash($password)
                )
            )
        );
    }

    public function buildHAshWithoutCard(string $orderId, string $formattedAmount, $password): string
    {
        return strtoupper(
            sha1(
                sprintf(
                    '%s%s%s%s',
                    $orderId,
                    $this->configuration->getTerminalId(),
                    $formattedAmount,
                    $this->generateSecurityHash($password)
                )
            )
        );
    }

    /**
     * @param string $password
     * @return string
     */
    private function generateSecurityHash(string $password): string
    {
        $tidPrefix  = str_repeat('0', 9 - strlen($this->configuration->getTerminalId()));
        $terminalId = sprintf('%s%s', $tidPrefix, $this->configuration->getTerminalId());
        return strtoupper(SHA1(sprintf('%s%s', $password, $terminalId)));
    }

    /**
     * @return array
     */
    private function buildCustomer(): array
    {
        return [
            'IPAddress'    => '127.0.0.1',
            'EmailAddress' => 'dummy@dummy.net'
        ];
    }
}
