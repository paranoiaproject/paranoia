<?php
namespace Paranoia\Processor\NestPay;

class SaleResponseProcessor extends BaseResponseProcessor
{
    public function process($rawResponse)
    {
        return $this->processCommonResponse($rawResponse);
    }
}
