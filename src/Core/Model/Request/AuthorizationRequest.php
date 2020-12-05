<?php
namespace Paranoia\Core\Model\Request;

/**
 * Class AuthorizationRequest
 * @package Paranoia\Core\Model\Request
 */
class AuthorizationRequest
{
    /** @var string */
    private $orderId;

    /** @var float */
    private $amount;

    /** @var string */
    private $currency;

    /** @var integer */
    private $installment;

    /** @var Card */
    private $card;

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     * @return AuthorizationRequest
     */
    public function setOrderId(string $orderId): AuthorizationRequest
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return AuthorizationRequest
     */
    public function setAmount(float $amount): AuthorizationRequest
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return AuthorizationRequest
     */
    public function setCurrency(string $currency): AuthorizationRequest
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return int
     */
    public function getInstallment(): int
    {
        return $this->installment;
    }

    /**
     * @param int $installment
     * @return AuthorizationRequest
     */
    public function setInstallment(int $installment): AuthorizationRequest
    {
        $this->installment = $installment;
        return $this;
    }

    /**
     * @return Card
     */
    public function getCard(): Card
    {
        return $this->card;
    }

    /**
     * @param Card $card
     * @return AuthorizationRequest
     */
    public function setCard(Card $card): AuthorizationRequest
    {
        $this->card = $card;
        return $this;
    }
}