<?php
namespace Paranoia\Payment\Response;

use Paranoia\Payment\TransferInterface;

interface ResponseInterface extends TransferInterface
{

    /**
     * returns boolean result indicating whether
     * transaction status is successful.
     *
     * @return boolean
     */
    public function isSuccess();

    /**
     * sets transaction status to response object.
     *
     * @param bool $isSuccess
     * @return self
     */
    public function setIsSuccess($isSuccess);

    /**
     * returns transaction type.
     *
     * @return string
     */
    public function getTransactionType();

    /**
     * sets transaction type to response object.
     *
     * @param string $transactionType
     * @return self
     */
    public function setTransactionType($transactionType);

    /**
     * returns order identity.
     *
     * @return string
     */
    public function getOrderId();

    /**
     * sets order identity to response object.
     *
     * @param string $orderId
     * @return self
     */
    public function setOrderId($orderId);

    /**
     * returns response code.
     *
     * @return integer.
     */
    public function getResponseCode();

    /**
     * sets response code to response object.
     *
     * @param integer $responseCode
     * @return self
     */
    public function setResponseCode($responseCode);

    /**
     * returns response message.
     *
     * @return string
     */
    public function getResponseMessage();

    /**
     * sets response message to response object.
     *
     * @param string $responseMessage
     * @return self
     */
    public function setResponseMessage($responseMessage);
}
