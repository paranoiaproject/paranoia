<?php
namespace Paranoia\Acquirer\Gvp\ResponseParser;

class AuthorizationResponseParser extends BaseResponseParser
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
