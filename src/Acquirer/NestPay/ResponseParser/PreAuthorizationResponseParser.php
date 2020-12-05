<?php
namespace Paranoia\Acquirer\NestPay\ResponseParser;

use Paranoia\Acquirer\NestPay\ResponseParser\BaseResponseParser;

class PreAuthorizationResponseParser extends BaseResponseParser
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
