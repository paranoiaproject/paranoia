<?php
namespace Paranoia\Acquirer\Gvp\ResponseParser;

use Paranoia\Acquirer\Gvp\ResponseParser\BaseResponseParser;

class SaleResponseParser extends BaseResponseParser
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
