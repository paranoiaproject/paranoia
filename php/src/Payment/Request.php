<?php
namespace Payment;

class Request
{
    /**
    * @var string
    */
    private $_orderId;

    /**
    * @var float
    */
    private $_amount;

    /**
    * @var string
    */
    private $_currency;

    /**
    * @var integer
    */
    private $_installment;

    /**
    * @var numeric
    */
    private $_cardNumber;

    /**
    * @var integer
    */
    private $_securityCode;

    /**
    * @var integer
    */
    private $_expireYear;

    /**
    * @var integer
    */
    private $_expireMonth;

    /**
    * @var string
    */
    private $_transactionId;

    /**
    * @var string
    */
    private $_rawData;

    /**
    * returns order identity.
    * @return string
    */
    public function getOrderId()
    {
        return $this->_orderId;
    }
    
    /**
    * sets order identity to request object.
    * @param $orderId
    */
    public function setOrderId($orderId)
    {
        $this->_orderId = $orderId;
    }
    
    /**
    * returns order amount.
    * @return float
    */
    public function getAmount()
    {
        return $this->_amount;
    }

    /**
    * sets order amount to request object.
    * @param float $amount
    */
    public function setAmount($amount)
    {
        $this->_amount = $amount;
    }

    /**
    * returns currency code string which is three digit.
    * @return string
    */
    public function getCurrency()
    {
        return $this->_currency;
    }

    /**
    * sets currency code to request object.
    * @param string $currency
    */
    public function setCurrency($currency)
    {
        $this->_currency = $currency;
    }
        
    /**
    * returns installment amount.
    * @return integer
    */
    public function getInstallment()
    {
        return $this->_installment;
    }

    /**
    * set installment amount to object.
    * @param integer $installment.
    */
    public function setInstallment($installment)
    {
        $this->_installment = $installment;
    }
    
    /**
    * returns card number.
    * @return numeric
    */
    public function getCardNumber()
    {
        return $this->_cardNumber;
    }

    /**
    * sets card number to request object.
    * @param numeric $cardNumber
    */
    public function setCardNumber($cardNumber)
    {
        $this->_cardNumber = $cardNumber;
    }
    
    /**
    * returns card security code.
    * @return string
    */
    public function getSecurityCode()
    {
        return $this->_securityCode;
    }
    
    /**
    * sets card security code to request object.
    * @param string $securityCode
    */
    public function setSecurityCode($securityCode)
    {
        $this->_securityCode = $securityCode;
    }
    
    /**
    * returns expire month of card.
    * @return integer
    */
    public function getExpireMonth()
    {
        return $this->_expireMonth;
    }
    
    /**
    * sets card expire month to request object.
    * @param integer $expireMonth
    */
    public function setExpireMonth($expireMonth)
    {
        $this->_expireMonth = $expireMonth;
    }

    /**
    * returns expire year of card.
    * @return integer
    */
    public function getExpireYear()
    {
        return $this->_expireYear;
    }

    /**
    * sets card expire year to request object.
    * @param integer $expireYear
    */
    public function setExpireYear($expireYear)
    {
        $this->_expireYear = $expireYear;
    }

    /**
    * returns transaction id.
    * @return string
    */
    public function getTransactionId()
    {
        return $this->_transactionId;
    }

    /**
    * sets transaction id to request object.
    * @return string
    */
    public function setTransactionId($transactionId)
    {
        $this->_transactionId = $transactionId;
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
