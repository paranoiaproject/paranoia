<?php
namespace Paranoia\Response;

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
     * {@inheritdoc}
     * @see \Payment\Response\ResponseInterface::isSuccess()
     */
    public function isSuccess()
    {
        return $this->isSuccess;
    }

    /**
     * {@inheritdoc}
     * @see \Payment\Response\ResponseInterface::setIsSuccess()
     */
    public function setIsSuccess($isSuccess)
    {
        $this->isSuccess = $isSuccess;
        return $this;
    }

    /**
     * {@inheritdoc}
     * @see \Payment\Response\ResponseInterface::getTransactionType()
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * {@inheritdoc}
     * @see \Payment\Response\ResponseInterface::setTransactionType()
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
        return $this;
    }

    /**
     * {@inheritdoc}
     * @see \Payment\Response\ResponseInterface::getOrderId()
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * {@inheritdoc}
     * @see \Payment\Response\ResponseInterface::setOrderId()
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * {@inheritdoc}
     * @see \Payment\Response\ResponseInterface::getTransactionId()
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     * @see \Payment\Response\ResponseInterface::setResponseCode()
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;
        return $this;
    }

    /**
     * {@inheritdoc}
     * @see \Payment\Response\ResponseInterface::getResponseCode()
     */
    public function getResponseMessage()
    {
        return $this->responseMessage;
    }

    /**
     * {@inheritdoc}
     * @see \Payment\Response\ResponseInterface::setResponseMessage()
     */
    public function setResponseMessage($responseMessage)
    {
        $this->responseMessage = $responseMessage;
        return $this;
    }
}
