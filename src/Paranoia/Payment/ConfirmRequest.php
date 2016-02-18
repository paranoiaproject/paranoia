<?php
namespace Paranoia\Payment;

class ConfirmRequest implements TransferInterface
{
    private $request;

    private $payload;

    public function __construct(Request $request = null, $payload = null)
    {
        $this->request = $request;
        $this->payload = $payload;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setPayload($payload)
    {
        $this->payload = $payload;
        return $this;
    }

    public function getPayload()
    {
        return $this->payload;
    }
}
