<?php
namespace Paranoia\Configuration;

class GvpConfiguration
{
    /** @var string */
    private $apiUrl;

    /** @var string */
    private $terminalId;

    /** @var string */
    private $merchantId;

    /** @var string */
    private $authorizationUsername;

    /** @var string */
    private $authorizationPassword;

    /** @var string */
    private $refundUsername;

    /** @var string */
    private $refundPassword;

    /** @var string */
    private $mode;

    /**
     * @return string
     */
    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     * @param string $apiUrl
     */
    public function setApiUrl(string $apiUrl): void
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * @return string
     */
    public function getTerminalId(): string
    {
        return $this->terminalId;
    }

    /**
     * @param string $terminalId
     */
    public function setTerminalId(string $terminalId): void
    {
        $this->terminalId = $terminalId;
    }

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * @param string $merchantId
     */
    public function setMerchantId(string $merchantId): void
    {
        $this->merchantId = $merchantId;
    }

    /**
     * @return string
     */
    public function getAuthorizationUsername(): string
    {
        return $this->authorizationUsername;
    }

    /**
     * @param string $authorizationUsername
     */
    public function setAuthorizationUsername(string $authorizationUsername): void
    {
        $this->authorizationUsername = $authorizationUsername;
    }

    /**
     * @return string
     */
    public function getAuthorizationPassword(): string
    {
        return $this->authorizationPassword;
    }

    /**
     * @param string $authorizationPassword
     */
    public function setAuthorizationPassword(string $authorizationPassword): void
    {
        $this->authorizationPassword = $authorizationPassword;
    }

    /**
     * @return string
     */
    public function getRefundUsername(): string
    {
        return $this->refundUsername;
    }

    /**
     * @param string $refundUsername
     */
    public function setRefundUsername(string $refundUsername): void
    {
        $this->refundUsername = $refundUsername;
    }

    /**
     * @return string
     */
    public function getRefundPassword(): string
    {
        return $this->refundPassword;
    }

    /**
     * @param string $refundPassword
     */
    public function setRefundPassword(string $refundPassword): void
    {
        $this->refundPassword = $refundPassword;
    }

    /**
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }
}
