<?php
namespace Paranoia\Core\Model\Request;

/**
 * Class ChargeRequest
 * @package Paranoia\Core\Model\Request
 */
class ChargeRequest
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
     * @return ChargeRequest
     */
    public function setOrderId(string $orderId): ChargeRequest
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
     * @return ChargeRequest
     */
    public function setAmount(float $amount): ChargeRequest
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
     * @return ChargeRequest
     */
    public function setCurrency(string $currency): ChargeRequest
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
     * @return ChargeRequest
     */
    public function setInstallment(int $installment): ChargeRequest
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
     * @return ChargeRequest
     */
    public function setCard(Card $card): ChargeRequest
    {
        $this->card = $card;
        return $this;
    }
}