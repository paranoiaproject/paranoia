<?php
namespace Paranoia\Core\Model\Request;

/**
 * Class CaptureRequest
 * @package Paranoia\Core\Model\Request
 */
class CaptureRequest
{
    /** @var string */
    private $orderId;

    /** @var string */
    private $transactionId;

    /** @var float */
    private $amount;

    /** @var string */
    private $currency;

    /** @var int */
    private $installment;

    /**
     * @return string
     */
    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    /**
     * @param string $transactionId
     * @return CaptureRequest
     */
    public function setTransactionId(string $transactionId): CaptureRequest
    {
        $this->transactionId = $transactionId;
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
     * @return CaptureRequest
     */
    public function setInstallment(int $installment): CaptureRequest
    {
        $this->installment = $installment;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     * @return CaptureRequest
     */
    public function setOrderId(string $orderId): CaptureRequest
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
     * @return CaptureRequest
     */
    public function setAmount(float $amount): CaptureRequest
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
     * @return CaptureRequest
     */
    public function setCurrency(string $currency): CaptureRequest
    {
        $this->currency = $currency;
        return $this;
    }
}