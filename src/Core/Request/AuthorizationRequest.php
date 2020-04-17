<?php
namespace Paranoia\Core\Request;

class AuthorizationRequest
{
    /** @var string */
    private $orderId;

    /** @var float */
    private $amount;

    /** @var string */
    private $currency;

    /** @var int */
    private $installment;

    /** @var string */
    private $cardNumber;

    /** @var string */
    private $cardCvv;

    /** @var int */
    private $cardExpireMonth;

    /** @var int */
    private $cardExpireYear;

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     */
    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
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
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
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
     */
    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return int
     */
    public function getInstallment(): ?int
    {
        return $this->installment;
    }

    /**
     * @param int $installment
     */
    public function setInstallment(?int $installment): void
    {
        $this->installment = $installment;
    }

    /**
     * @return string
     */
    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }

    /**
     * @param string $cardNumber
     */
    public function setCardNumber(string $cardNumber): void
    {
        $this->cardNumber = $cardNumber;
    }

    /**
     * @return string
     */
    public function getCardCvv(): string
    {
        return $this->cardCvv;
    }

    /**
     * @param string $cardCvv
     */
    public function setCardCvv(string $cardCvv): void
    {
        $this->cardCvv = $cardCvv;
    }

    /**
     * @return int
     */
    public function getCardExpireMonth(): int
    {
        return $this->cardExpireMonth;
    }

    /**
     * @param int $cardExpireMonth
     */
    public function setCardExpireMonth(int $cardExpireMonth): void
    {
        $this->cardExpireMonth = $cardExpireMonth;
    }

    /**
     * @return int
     */
    public function getCardExpireYear(): int
    {
        return $this->cardExpireYear;
    }

    /**
     * @param int $cardExpireYear
     */
    public function setCardExpireYear(int $cardExpireYear): void
    {
        $this->cardExpireYear = $cardExpireYear;
    }
}
