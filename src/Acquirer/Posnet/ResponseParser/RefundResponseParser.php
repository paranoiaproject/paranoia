<?php
namespace Paranoia\Acquirer\Posnet\ResponseParser;

use Paranoia\Acquirer\Posnet\ResponseParser\BaseResponseParser;

class RefundResponseParser extends BaseResponseParser
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
