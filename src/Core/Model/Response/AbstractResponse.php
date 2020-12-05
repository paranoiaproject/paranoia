<?php
namespace Paranoia\Core\Model\Response;


abstract class AbstractResponse
{
    /** @var bool */
    private $approved;

    /** @var string */
    private $errorCode;

    /** @var string */
    private $errorMessage;

    /**
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->approved;
    }

    /**
     * @param bool $approved
     */
    public function setApproved(bool $approved): void
    {
        $this->approved = $approved;
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * @param string $errorCode
     */
    public function setErrorCode(string $errorCode): void
    {
        $this->errorCode = $errorCode;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     */
    public function setErrorMessage(string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }
}