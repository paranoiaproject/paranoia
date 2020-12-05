<?php
namespace Paranoia\Core\Model\Request\Resource;

class Card implements ResourceInterface
{
    /** @var string */
    private $number;

    /** @var string */
    private $securityCode;

    /** @var integer */
    private $expireMonth;

    /** @var integer */
    private $expireYear;

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     * @return Card
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @return string
     */
    public function getSecurityCode()
    {
        return $this->securityCode;
    }

    /**
     * @param string $securityCode
     * @return Card
     */
    public function setSecurityCode($securityCode)
    {
        $this->securityCode = $securityCode;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpireMonth()
    {
        return $this->expireMonth;
    }

    /**
     * @param int $expireMonth
     * @return Card
     */
    public function setExpireMonth($expireMonth)
    {
        $this->expireMonth = $expireMonth;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpireYear()
    {
        return $this->expireYear;
    }

    /**
     * @param int $expireYear
     * @return Card
     */
    public function setExpireYear($expireYear)
    {
        $this->expireYear = $expireYear;
        return $this;
    }
}
