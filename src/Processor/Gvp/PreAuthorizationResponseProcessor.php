<?php
namespace Paranoia\Processor\Gvp;

class PreAuthorizationResponseProcessor extends BaseResponseProcessor
{
    public function process($rawResponse)
    {
        return $this->processCommonResponse($rawResponse);
    }
}
