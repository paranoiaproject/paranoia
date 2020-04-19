<?php
namespace Paranoia\Core\Request;

class RefundRequest
{
    /** @var string */
    private $transactionRef;

    /** @var float */
    private $amount;

    /** @var string */
    private $currency;

    /**
     * @return string
     */
    public function getTransactionRef(): string
    {
        return $this->transactionRef;
    }

    /**
     * @param string $transactionRef
     */
    public function setTransactionRef(string $transactionRef): void
    {
        $this->transactionRef = $transactionRef;
    }

    /**
     * @return float
     */
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(?float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }
}
