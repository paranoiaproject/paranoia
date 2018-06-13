<?php
namespace Paranoia\Processor\NestPay;

class RefundResponseProcessor extends BaseResponseProcessor
{
    public function process($rawResponse)
    {
        return $this->processCommonResponse($rawResponse);
    }
}
