<?php
namespace Paranoia\Processor\Posnet;

class PreAuthorizationResponseProcessor extends BaseResponseProcessor
{
    public function process($rawResponse)
    {
        return $this->processCommonResponse($rawResponse);
    }
}
