<?php
namespace Paranoia\Payment;

use Symfony\Component\EventDispatcher\Event;

class PaymentEventArg extends Event
{
    /**
     * @var \Paranoia\Payment\Request
     */
    private $request;

    /**
     * @var \Paranoia\Payment\Response\ResponseInterface
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
        $transactionType = $transactionType;
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
     * @param \Paranoia\Payment\Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return \Paranoia\Payment\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param \Paranoia\Payment\Response\ResponseInterface $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return \Paranoia\Payment\Response\ResponseInterface
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
