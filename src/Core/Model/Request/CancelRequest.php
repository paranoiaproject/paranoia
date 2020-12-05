<?php
namespace Paranoia\Core\Model\Request;

/**
 * Class CancelRequest
 * @package Paranoia\Core\Model\Request
 */
class CancelRequest
{
    /** @var string */
    private $orderId;

    /** @var string */
    private $transactionId;

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     * @return CancelRequest
     */
    public function setOrderId(string $orderId): CancelRequest
    {
        $this->orderId = $orderId;
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
     * @return CancelRequest
     */
    public function setTransactionId(string $transactionId): CancelRequest
    {
        $this->transactionId = $transactionId;
        return $this;
    }
}