<?php

namespace Paranoia\Configuration;


class Gvp extends AbstractConfiguration
{

    /**
     * @var int
     */
    private $terminalId;

    /**
     * @var int
     */
    private $merchantId;

    /**
     * @var string
     */
    private $authorizationUsername;

    /**
     * @var string
     */
    private $authorizationPassword;

    /**
     * @var string
     */
    private $refundUsername;

    /**
     * @var string
     */
    private $refundPassword;

    /**
     * @var string
     */
    private $mode;

    /**
     * @param string $authorizationPassword
     */
    public function setAuthorizationPassword($authorizationPassword)
    {
        $this->authorizationPassword = $authorizationPassword;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorizationPassword()
    {
        return $this->authorizationPassword;
    }

    /**
     * @param string $authorizationUsername
     */
    public function setAuthorizationUsername($authorizationUsername)
    {
        $this->authorizationUsername = $authorizationUsername;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorizationUsername()
    {
        return $this->authorizationUsername;
    }

    /**
     * @param int $merchantId
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
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $refundPassword
     */
    public function setRefundPassword($refundPassword)
    {
        $this->refundPassword = $refundPassword;
        return $this;
    }

    /**
     * @return string
     */
    public function getRefundPassword()
    {
        return $this->refundPassword;
    }

    /**
     * @param string $refundUsername
     */
    public function setRefundUsername($refundUsername)
    {
        $this->refundUsername = $refundUsername;
        return $this;
    }

    /**
     * @return string
     */
    public function getRefundUsername()
    {
        return $this->refundUsername;
    }

    /**
     * @param int $terminalId
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

}
