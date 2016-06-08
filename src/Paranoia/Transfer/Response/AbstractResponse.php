<?php
namespace Paranoia\Transfer\Response;


abstract class AbstractResponse implements ResponseInterface
{
    /**
     * @var bool
     */
    private $isSuccess;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $message;

    /**
     * @return boolean
     */
    public function isIsSuccess()
    {
        return $this->isSuccess;
    }

    /**
     * @param boolean $isSuccess
     * @return AbstractResponse
     */
    public function setIsSuccess($isSuccess)
    {
        $this->isSuccess = $isSuccess;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return AbstractResponse
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return AbstractResponse
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
}