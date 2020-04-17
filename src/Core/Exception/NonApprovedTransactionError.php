<?php
namespace Paranoia\Core\Exception;

class NonApprovedTransactionError extends \Exception
{
    /** @var string */
    private $details;

    public function __construct($message, $code, $details)
    {
        parent::__construct($message, $code, null);
        $this->details = $details;
    }

    /**
     * @return string
     */
    public function getDetails(): string
    {
        return $this->details;
    }
}
