<?php
namespace Paranoia\Processor\NestPay;

class PostAuthorizationResponseProcessor extends BaseResponseProcessor
{
    public function process($rawResponse)
    {
        return $this->processCommonResponse($rawResponse);
    }
}
