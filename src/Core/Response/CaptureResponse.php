<?php
namespace Paranoia\Core\Response;

class CaptureResponse
{
    /** @var string */
    private $transactionId;

    /** @var string */
    private $authCode;

    /**
     * CaptureResponse constructor.
     * @param string $transactionId
     * @param string $authCode
     */
    public function __construct(?string $transactionId, ?string $authCode)
    {
        $this->transactionId = $transactionId;
        $this->authCode = $authCode;
    }

    /**
     * @return string
     */
    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    /**
     * @return string
     */
    public function getAuthCode(): ?string
    {
        return $this->authCode;
    }
}
