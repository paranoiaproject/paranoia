<?php
namespace Paranoia\Processor\Gvp;

class CancelResponseProcessor extends BaseResponseProcessor
{
    public function process($rawResponse)
    {
        return $this->processCommonResponse($rawResponse);
    }
}
