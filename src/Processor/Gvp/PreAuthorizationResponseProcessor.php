<?php
namespace Paranoia\Processor\Gvp;

class PreAuthorizationResponseProcessor extends BaseResponseProcessor
{
    /**
     * @param $rawResponse
     * @throws \Paranoia\Core\Exception\InvalidResponseException
     * @return \Paranoia\Response
     */
    public function process($rawResponse)
    {
        return $this->processCommonResponse($rawResponse);
    }
}
