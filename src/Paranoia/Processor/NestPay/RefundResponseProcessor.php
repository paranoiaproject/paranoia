<?php
namespace Paranoia\Processor\NestPay;

use Paranoia\Transfer\Response\RefundResponse;

class RefundResponseProcessor extends AbstractNestPayProcessor
{
    /**
     * @param $rawResponse
     * @return \Paranoia\Transfer\Response\ResponseInterface
     */
    public function process($rawResponse)
    {
        $response = new RefundResponse();
        $this->prepareResponse($rawResponse, $response);
        return $response;
    }
}