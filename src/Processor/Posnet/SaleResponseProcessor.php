<?php
namespace Paranoia\Processor\Posnet;

class SaleResponseProcessor extends BaseResponseProcessor
{
    public function process($rawResponse)
    {
        return $this->processCommonResponse($rawResponse);
    }
}
