<?php
namespace Paranoia\Core\Response;

class RefundResponse
{
    /** @var string */
    private $hostReference;

    /** @var string */
    private $authCode;

    /**
     * AuthorizationResponse constructor.
     * @param string $hostReference
     * @param string $authCode
     */
    public function __construct(string $hostReference, string $authCode)
    {
        $this->hostReference = $hostReference;
        $this->authCode = $authCode;
    }

    /**
     * @return string
     */
    public function getHostReference(): string
    {
        return $this->hostReference;
    }

    /**
     * @param string $hostReference
     */
    public function setHostReference(string $hostReference): void
    {
        $this->hostReference = $hostReference;
    }

    /**
     * @return string
     */
    public function getAuthCode(): string
    {
        return $this->authCode;
    }

    /**
     * @param string $authCode
     */
    public function setAuthCode(string $authCode): void
    {
        $this->authCode = $authCode;
    }
}
