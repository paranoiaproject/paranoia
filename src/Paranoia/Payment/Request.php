<?php
namespace Paranoia\Payment;

class Request implements TransferInterface
{

    /**
     * @var string
     */
    private $orderId;

    /**
     * @var string
     */
    private $groupId;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var integer
     */
    private $installment;

    /**
     * @var string
     */
    private $cardNumber;

    /**
     * @var string
     */
    private $cardHolderName;

    /**
     * @var integer
     */
    private $securityCode;

    /**
     * @var integer
     */
    private $expireYear;

    /**
     * @var integer
     */
    private $expireMonth;

    /**
     * @var string
     */
    private $transactionId;

    /**
     * @var string
     */
    private $authCode;

    /**
     * @var string
     */
    private $ipAddress;

    /**
     * @var string
     */
    private $email;

    /**
     * returns order identity.
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * sets order identity to request object.
     *
     * @param $orderId
     *
     * @return self
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * returns order group identity. optional.
     *
     * @return string
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * sets order identity to request object.
     *
     * @param $groupId
     *
     * @return self
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
        return $this;
    }

    /**
     * returns order amount.
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * sets order amount to request object.
     *
     * @param float $amount
     *
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * returns currency code string which is three digit.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * sets currency code to request object.
     *
     * @param string $currency
     *
     * @return self
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * returns installment amount.
     *
     * @return integer
     */
    public function getInstallment()
    {
        return $this->installment;
    }

    /**
     * set installment amount to object.
     *
     * @param integer $installment
     *
     * @return self
     */
    public function setInstallment($installment)
    {
        $this->installment = $installment;
        return $this;
    }

    /**
     * returns card number.
     *
     * @return string
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * sets card number to request object.
     *
     * @param string $cardNumber Numeric value
     *
     * @return self
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;
        return $this;
    }

    /**
     * returns card holder name.
     *
     * @return string
     */
    public function getCardHolderName()
    {
        return $this->cardHolderName;
    }

    /**
     * sets card holder name to request object.
     *
     * @param string $cardHolderName Numeric value
     *
     * @return self
     */
    public function setCardHolderName($cardHolderName)
    {
        $this->cardHolderName = $cardHolderName;
        return $this;
    }

    /**
     * returns card security code.
     *
     * @return string
     */
    public function getSecurityCode()
    {
        return $this->securityCode;
    }

    /**
     * sets card security code to request object.
     *
     * @param string $securityCode
     *
     * @return self
     */
    public function setSecurityCode($securityCode)
    {
        $this->securityCode = $securityCode;
        return $this;
    }

    /**
     * returns expire month of card.
     *
     * @return integer
     */
    public function getExpireMonth()
    {
        return $this->expireMonth;
    }

    /**
     * sets card expire month to request object.
     *
     * @param integer $expireMonth
     *
     * @return self
     */
    public function setExpireMonth($expireMonth)
    {
        $this->expireMonth = $expireMonth;
        return $this;
    }

    /**
     * returns expire year of card.
     *
     * @return integer
     */
    public function getExpireYear()
    {
        return $this->expireYear;
    }

    /**
     * sets card expire year to request object.
     *
     * @param integer $expireYear
     *
     * @return self
     */
    public function setExpireYear($expireYear)
    {
        $this->expireYear = $expireYear;
        return $this;
    }

    /**
     * returns transaction id.
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * sets transaction id to request object.
     *
     * @param string $transactionId
     *
     * @return self
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
     * returns ip address.
     *
     * @return string
     */
    public function getIPAddress()
    {
        return $this->ipAddress;
    }

    /**
     * sets ip address to request object.
     *
     * @param string $ipAddress
     *
     * @return self
     */
    public function setIPAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    /**
     * returns email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * sets email to request object.
     *
     * @param string $email
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }
}
