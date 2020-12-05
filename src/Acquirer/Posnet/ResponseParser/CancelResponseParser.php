<?php
namespace Paranoia\Acquirer\Posnet\ResponseParser;

class CancelResponseParser extends BaseResponseParser
{
    /**
     * @param $rawResponse
     * @throws \Paranoia\Core\Exception\BadResponseException
     * @return \Paranoia\Core\Model\Response
     */
    public function parse($rawResponse)
    {
        return $this->processCommonResponse($rawResponse);
    }
}
