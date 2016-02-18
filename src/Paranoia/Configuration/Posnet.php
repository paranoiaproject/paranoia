<?php
namespace Paranoia\Configuration;

class Posnet extends AbstractConfiguration
{
    /**
     * @var int
     */
    private $merchantId;

    /**
     * @var int
     */
    private $terminalId;

    /**
     * @var int
     */
    private $terminal3DId;

    /**
     * @var int
     */
    private $posnetId;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $secureKey;

    /**
     * @var string
     */
    private $jokerVadaa;

    /**
     * @param int $merchantId
     *
     * @return $this
     */
    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;
        return $this;
    }

    /**
     * @return int
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * @param int $terminalId
     *
     * @return $this
     */
    public function setTerminalId($terminalId)
    {
        $this->terminalId = $terminalId;
        return $this;
    }

    /**
     * @return int
     */
    public function getTerminalId()
    {
        return $this->terminalId;
    }

    /**
     * @param int $terminal3DId
     *
     * @return $this
     */
    public function setTerminal3DId($terminal3DId)
    {
        $this->terminal3DId = $terminal3DId;
        return $this;
    }

    /**
     * @return int
     */
    public function getTerminal3DId()
    {
        return $this->terminal3DId;
    }

    /**
     * @param int $posnetId
     *
     * @return $this
     */
    public function setPosnetId($posnetId)
    {
        $this->posnetId = $posnetId;
        return $this;
    }

    /**
     * @return int
     */
    public function getPosnetId()
    {
        return $this->posnetId;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param int $secureKey
     *
     * @return $this
     */
    public function setSecureKey($secureKey)
    {
        $this->secureKey = $secureKey;
        return $this;
    }

    /**
     * @return int
     */
    public function getSecureKey()
    {
        return $this->secureKey;
    }

    /**
     * @param int $jokerVadaa
     *
     * @return $this
     */
    public function setJokerVadaa($jokerVadaa)
    {
        $this->jokerVadaa = $jokerVadaa;
        return $this;
    }

    /**
     * @return int
     */
    public function getJokerVadaa()
    {
        return $this->jokerVadaa;
    }
}
