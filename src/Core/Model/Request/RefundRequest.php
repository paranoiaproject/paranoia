<?php
namespace Paranoia\Core\Model\Request;

/**
 * Class RefundRequest
 * @package Paranoia\Core\Model\Request
 */
class RefundRequest
{
    /** @var string */
    private $orderId;

    /** @var float */
    private $amount;

    /** @var string */
    private $currency;

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     * @return RefundRequest
     */
    public function setOrderId(string $orderId): RefundRequest
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
     * @return RefundRequest
     */
    public function setAmount(float $amount): RefundRequest
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
     * @return RefundRequest
     */
    public function setCurrency(string $currency): RefundRequest
    {
        $this->currency = $currency;
        return $this;
    }
}