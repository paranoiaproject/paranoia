<?php
namespace Paranoia\Processor\Gvp;

class RefundResponseProcessor extends BaseResponseProcessor
{
    public function process($rawResponse)
    {
        return $this->processCommonResponse($rawResponse);
    }
}
