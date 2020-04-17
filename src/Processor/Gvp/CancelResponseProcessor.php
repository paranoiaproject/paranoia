<?php
namespace Paranoia\Processor\Gvp;

class CancelResponseProcessor extends BaseResponseProcessor
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
