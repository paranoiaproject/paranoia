<?php
namespace Payment\Response;

abstract class ResponseAbstract
{
    /**
    * @var boolean
    */
    protected $_isSuccess;

    /**
    * @var string
    */
    protected $_transactionType;

    /**
    * @var string
    */
    protected $_orderId;

    /**
    * @var string
    */
    protected $_transactionId;

    /**
    * @var integer
    */
    protected $_responseCode;
    
    /**
    * @var string
    */
    protected $_responseMessage;

    /**
    * @var string
    */
    protected $_rawData;
    
    /**
    * @see \Payment\Response\ResponseInterface::isSuccess()
    */
    public function isSuccess()
    {
        return $this->_isSuccess;
    }
    
    /**
    * @see \Payment\Response\ResponseInterface::setIsSuccess()
    */
    public function setIsSuccess($isSuccess)
    {
        $this->_isSuccess = $isSuccess;
    }

    /**
    * @see \Payment\Response\ResponseInterface::getTransactionType()
    */
    public function getTransactionType()
    {
        return $this->_transactionType;
    }
    
    /**
    * @see \Payment\Response\ResponseInterface::setTransactionType()
    */
    public function setTransactionType($transactionType)
    {
        $this->_transactionType = $transactionType;
    }
    
    /**
    * @see \Payment\Response\ResponseInterface::getOrderId()
    */
    public function getOrderId()
    {
        return $this->_orderId;
    }
    
    /**
    * @see \Payment\Response\ResponseInterface::setOrderId()
    */
    public function setOrderId($orderId)
    {
        $this->_orderId = $orderId;
    }
    
    /**
    * @see \Payment\Response\ResponseInterface::getTransactionId()
    */
    public function getTransactionId()
    {
        return $this->_transactionId;
    }
    
    /**
    * @see \Payment\Response\ResponseInterface::setTransactionId()
    */
    public function setTransactionId($transactionId)
    {
        $this->_transactionId = $transactionId;
    }

    /**
    * @see \Payment\Response\ResponseInterface::getResponseCode()
    */
    public function getResponseCode()
    {
        return $this->_responseCode;
    }
    
    /**
    * @see \Payment\Response\ResponseInterface::setResponseCode()
    */
    public function setResponseCode($responseCode)
    {
        $this->_responseCode =  $responseCode;
    }
    
    /**
    * @see \Payment\Response\ResponseInterface::getResponseCode()
    */
    public function getResponseMessage()
    {
        return $this->_responseMessage;
    }
    
    /**
    * @see \Payment\Response\ResponseInterface::setResponseMessage()
    */
    public function setResponseMessage($responseMessage)
    {
        $this->_responseMessage = $responseMessage;
    }

    /**
    * returns request as raw data.
    * @return string
    */
    public function getRawData()
    {
        return $this->_rawData;
    }
    
    /**
    * sets response data as raw.
    * @param string $rawData
    */
    public function setRawData($rawData)
    {
        $this->_rawData = $rawData;
    }
}
