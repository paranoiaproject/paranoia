<?php
namespace Paranoia\Acquirer\Posnet\RequestBuilder;

use Paranoia\Acquirer\Posnet\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\Posnet\PosnetConfiguration;
use Paranoia\Core\Model\Request\Card;

/**
 * Class RequestBuilderCommon
 * @package Paranoia\Acquirer\Posnet\RequestBuilder
 */
class RequestBuilderCommon
{
    public const ENVELOPE_NAME    = 'posnetRequest';
    public const FORM_FIELD = 'xmldata';

    /** @var PosnetConfiguration */
    private $configuration;

    /** @var  ExpireDateFormatter */
    protected $expireDateFormatter;

    /**
     * RequestBuilderCommon constructor.
     * @param PosnetConfiguration $configuration
     * @param ExpireDateFormatter $expireDateFormatter
     */
    public function __construct(PosnetConfiguration $configuration, ExpireDateFormatter $expireDateFormatter)
    {
        $this->configuration = $configuration;
        $this->expireDateFormatter = $expireDateFormatter;
    }

    /**
     * @return array
     */
    public function buildBaseRequest(): array
    {
        return [
            'mid' => $this->configuration->getMerchantId(),
            'tid' => $this->configuration->getTerminalId(),
            'username' => $this->configuration->getUsername(),
            'password' => $this->configuration->getPassword()
        ];
    }

    /**
     * @param Card $card
     * @return array
     */
    public function buildCard(Card $card): array
    {
        return [
            'ccno' => $card->getNumber(),
            'cvc' => $card->getCvv(),
            'expDate' => $this->expireDateFormatter->format(
                [
                    $card->getExpireMonth(),
                    $card->getExpireYear()
                ]
            )
        ];
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
}
