<?php
namespace Paranoia\Acquirer\NestPay\RequestBuilder;

use Paranoia\Acquirer\NestPay\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\NestPay\NestPayConfiguration;
use Paranoia\Core\Model\Request\Card;

/**
 * Class RequestBuilderCommon
 * @package Paranoia\Acquirer\NestPay\RequestBuilder
 */
class RequestBuilderCommon
{
    public const ENVELOPE_NAME    = 'CC5Request';
    public const FORM_FIELD = 'DATA';

    /** @var NestPayConfiguration */
    private $configuration;

    /** @var  ExpireDateFormatter */
    private $expireDateFormatter;

    /**
     * RequestBuilderCommon constructor.
     * @param NestPayConfiguration $configuration
     * @param ExpireDateFormatter $expireDateFormatter
     */
    public function __construct(NestPayConfiguration $configuration, ExpireDateFormatter $expireDateFormatter)
    {
        $this->configuration = $configuration;
        $this->expireDateFormatter = $expireDateFormatter;
    }

    /**
     * @param string $type
     * @return array
     */
    public function buildBaseRequest(string $type): array
    {
        /** @var \Paranoia\Acquirer\NestPay\NestPayConfiguration $config */
        $config = $this->configuration;
        return [
            'Mode'     => $config->getMode(),
            'ClientId' => $config->getClientId(),
            'Name'     => $config->getUsername(),
            'Password' => $config->getPassword(),
            'Type'     =>  $type,
        ];
    }

    /**
     * @param Card $card
     * @return array
     */
    public function buildCard(Card $card): array
    {
        $expireDate = $this->expireDateFormatter->format(
            [
                $card->getExpireMonth(),
                $card->getExpireYear()
            ]
        );

        return array(
            'Number'     => $card->getNumber(),
            'Cvv2Val'    => $card->getCvv(),
            'Expires'    => $expireDate
        );
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
