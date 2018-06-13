<?php
namespace Paranoia\Processor\Posnet;

class RefundResponseProcessor extends BaseResponseProcessor
{
    public function process($rawResponse)
    {
        return $this->processCommonResponse($rawResponse);
    }
}
