<?php
namespace Paranoia\Core\Exception;

use Exception;

class UnapprovedTransactionException extends Exception
{
    /** @var string */
    private $details;

    /** @var string */
    private $errorCode;

    public function __construct(string $message, string $errorCode, ?string $details=null)
    {
        parent::__construct($message);
        $this->details = $details;
        $this->errorCode = $errorCode;
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function getDetails(): ?string
    {
        return $this->details;
    }
}
