<?php
namespace Paranoia\Transfer\Request;

abstract class AbstractRequest implements RequestInterface
{
    /**
     * @var string
     */
    private $orderId;

    /**
     * @var string
     */
    private $transactionId;

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     * @return AbstractRequest
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param string $transactionId
     * @return AbstractRequest
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
        return $this;
    }
}