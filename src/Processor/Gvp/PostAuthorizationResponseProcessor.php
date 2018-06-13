<?php
namespace Paranoia\Processor\Gvp;

class PostAuthorizationResponseProcessor extends BaseResponseProcessor
{
    public function process($rawResponse)
    {
        return $this->processCommonResponse($rawResponse);
    }
}
