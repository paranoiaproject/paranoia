<?php
namespace Paranoia\Core\Model\Response;

/**
 * Interface PaymentResponse
 * @package Paranoia\Core\Model\Response
 */
interface PaymentResponse
{
    /**
     * @return string
     */
    public function getOrderId(): string;

    /**
     * @return string
     */
    public function getTransactionId(): string;

    /**
     * @return string
     */
    public function getAuthCode(): string;
}