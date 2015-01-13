<?php
namespace Paranoia\Payment\Response;

abstract class ResponseAbstract
{

    /**
     * @var boolean
     */
    protected $isSuccess;

    /**
     * @var string
     */
    protected $transactionType;

    /**
     * @var string
     */
    protected $orderId;

    /**
     * @var string
     */
    protected $transactionId;

    /**
     * @var string
     */
    protected $authCode;

    /**
     * @var integer
     */
    protected $responseCode;

    /**
     * @var string
     */
    protected $responseMessage;

    /**
     * @var string
     */
    protected $rawData;

    /**
     * @var integer
     */
    protected $time;

    /**
     * @see \Payment\Response\ResponseInterface::isSuccess()
     */
    public function isSuccess()
    {
        return $this->isSuccess;
    }

    /**
     * @see \Payment\Response\ResponseInterface::setIsSuccess()
     */
    public function setIsSuccess($isSuccess)
    {
        $this->isSuccess = $isSuccess;
        return $this;
    }

    /**
     * @see \Payment\Response\ResponseInterface::getTransactionType()
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * @see \Payment\Response\ResponseInterface::setTransactionType()
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
        return $this;
    }

    /**
     * @see \Payment\Response\ResponseInterface::getOrderId()
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @see \Payment\Response\ResponseInterface::setOrderId()
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * @see \Payment\Response\ResponseInterface::getTransactionId()
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @see \Payment\Response\ResponseInterface::setTransactionId()
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    /**
     * returns auth code.
     *
     * @return string
     */
    public function getAuthCode()
    {
        return $this->authCode;
    }

    /**
     * sets auth code to request object.
     *
     * @param string $authCode
     *
     * @return self
     */
    public function setAuthCode($authCode)
    {
        $this->authCode = $authCode;
        return $this;
    }

    /**
     * @see \Payment\Response\ResponseInterface::getResponseCode()
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @see \Payment\Response\ResponseInterface::setResponseCode()
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;
        return $this;
    }

    /**
     * @see \Payment\Response\ResponseInterface::getResponseCode()
     */
    public function getResponseMessage()
    {
        return $this->responseMessage;
    }

    /**
     * @see \Payment\Response\ResponseInterface::setResponseMessage()
     */
    public function setResponseMessage($responseMessage)
    {
        $this->responseMessage = $responseMessage;
        return $this;
    }

    /**
     * returns request as raw data.
     *
     * @return string
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * sets response data as raw.
     *
     * @param string $rawData
     *
     * @return self
     */
    public function setRawData($rawData)
    {
        $this->rawData = $rawData;
        return $this;
    }

    /**
     * @see \Payment\TransferInterface::getTime()
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @see \Payment\TransferInterface::setTime()
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }
}
