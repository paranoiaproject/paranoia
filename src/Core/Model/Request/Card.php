<?php
namespace Paranoia\Core\Model\Request;

/**
 * Class Card
 * @package Paranoia\Core\Model\Request
 */
class Card
{
    /** @var string */
    private $number;

    /** @var int */
    private $expireMonth;

    /** @var int */
    private $expireYear;

    /** @var string */
    private $cvv;

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

    /**
     * @return string
     */
    public function getCvv()
    {
        return $this->cvv;
    }

    /**
     * @param string $cvv
     * @return Card
     */
    public function setCvv($cvv)
    {
        $this->cvv = $cvv;
        return $this;
    }
}