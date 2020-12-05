<?php
namespace Paranoia\Acquirer\Gvp\ResponseParser;

class CaptureResponseParser extends BaseResponseParser
{
    /**
     * @param $rawResponse
     * @throws \Paranoia\Core\Exception\BadResponseException
     * @return \Paranoia\Core\Model\Response
     */
    public function process($rawResponse)
    {
        return $this->processCommonResponse($rawResponse);
    }
}
