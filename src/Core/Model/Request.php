<?php
namespace Paranoia\Core\Model;

use Paranoia\Core\Model\Request\Card;

class Request implements TransferInterface
{
    /** @var string */
    private $orderId;

    /** @var float */
    private $amount;

    /** @var string */
    private $currency;

    /** @var integer */
    private $installment;

    /** @var string */
    private $transactionId;

    /** @var string */
    private $authCode;

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
     * @return Request
     */
    public function setOrderId(string $orderId): Request
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
     * @return Request
     */
    public function setAmount(float $amount): Request
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
     * @return Request
     */
    public function setCurrency(string $currency): Request
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
     * @return Request
     */
    public function setInstallment(int $installment): Request
    {
        $this->installment = $installment;
        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    /**
     * @param string $transactionId
     * @return Request
     */
    public function setTransactionId(string $transactionId): Request
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthCode(): string
    {
        return $this->authCode;
    }

    /**
     * @param string $authCode
     * @return Request
     */
    public function setAuthCode(string $authCode): Request
    {
        $this->authCode = $authCode;
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
     * @return Request
     */
    public function setCard(Card $card): Request
    {
        $this->card = $card;
        return $this;
    }
}
