<?php
namespace Paranoia\Core\Model\Response;

/**
 * Class CaptureResponse
 * @package Paranoia\Core\Model\Response
 */
class CaptureResponse extends AbstractResponse implements PaymentResponse
{
    /** @var string */
    private $orderId;

    /** @var string */
    private $transactionId;

    /** @var string */
    private $authCode;

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
     * @return string
     */
    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    /**
     * @param string $transactionId
     */
    public function setTransactionId(string $transactionId): void
    {
        $this->transactionId = $transactionId;
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
     */
    public function setAuthCode(string $authCode): void
    {
        $this->authCode = $authCode;
    }
}