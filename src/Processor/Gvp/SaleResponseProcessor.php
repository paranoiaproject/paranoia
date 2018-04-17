<?php
namespace Paranoia\Processor\Gvp;

class SaleResponseProcessor extends BaseResponseProcessor
{
    public function process($rawResponse)
    {
        return $this->processCommonResponse($rawResponse);
    }
}
