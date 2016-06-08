<?php
namespace Paranoia\Processor\NestPay;

use Paranoia\Transfer\Response\CancelResponse;

class CancelResponseProcessor extends AbstractNestPayProcessor
{
    /**
     * @param $rawResponse
     * @return \Paranoia\Transfer\Response\ResponseInterface
     */
    public function process($rawResponse)
    {
        $response = new CancelResponse();
        $this->prepareResponse($rawResponse, $response);
        return $response;
    }
}