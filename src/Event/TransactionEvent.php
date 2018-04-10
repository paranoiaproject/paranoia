<?php
namespace Paranoia\Event;

use Symfony\Component\EventDispatcher\Event;

class TransactionEvent extends Event
{
    /**
     * @var \Paranoia\Request
     */
    private $request;

    /**
     * @var \Paranoia\Response\ResponseInterface
     */
    private $response;

    /**
     * @var \Exception
     */
    private $exception;

    /**
     * @var string
     */
    private $transactionType;

    public function __construct($request = null, $response = null, $transactionType = null, $exception = null)
    {
        $this->request = $request;
        $this->response = $response;
        $this->transactionType = $transactionType;
        $this->exception = $exception;
    }

    /**
     * @param \Exception $exception
     */
    public function setException($exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param \Paranoia\Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return \Paranoia\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param \Paranoia\Response\ResponseInterface $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return \Paranoia\Response\ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param string $transactionType
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
    }

    /**
     * @return string
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }
}
