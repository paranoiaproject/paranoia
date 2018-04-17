<?php
namespace Paranoia\Processor\NestPay;

class PreAuthorizationResponseProcessor extends BaseResponseProcessor
{
    public function process($rawResponse)
    {
        return $this->processCommonResponse($rawResponse);
    }
}
