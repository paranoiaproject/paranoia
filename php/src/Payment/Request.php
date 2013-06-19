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
    * @return self
    */
    public function setOrderId($orderId)
    {
        $this->_orderId = $orderId;
        return $this;
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
    * @return self
    */
    public function setAmount($amount)
    {
        $this->_amount = $amount;
        return $this;
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
    * @return self
    */
    public function setCurrency($currency)
    {
        $this->_currency = $currency;
        return $this;
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
    * @return self
    */
    public function setInstallment($installment)
    {
        $this->_installment = $installment;
        return $this;
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
    * @return self
    */
    public function setCardNumber($cardNumber)
    {
        $this->_cardNumber = $cardNumber;
        return $this;
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
    * @return self
    */
    public function setSecurityCode($securityCode)
    {
        $this->_securityCode = $securityCode;
        return $this;
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
    * @return self
    */
    public function setExpireMonth($expireMonth)
    {
        $this->_expireMonth = $expireMonth;
        return $this;
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
    * @return self
    */
    public function setExpireYear($expireYear)
    {
        $this->_expireYear = $expireYear;
        return $this;
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
    * @return self
    */
    public function setTransactionId($transactionId)
    {
        $this->_transactionId = $transactionId;
        return $this;
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
    * @return self
    */
    public function setRawData($rawData)
    {
        $this->_rawData = $rawData;
        return $this;
    }
}
