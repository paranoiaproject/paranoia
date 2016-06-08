<?php
namespace Paranoia\Processor\NestPay;

use Paranoia\Transfer\Response\SaleResponse;

class SaleResponseProcessor extends AbstractNestPayProcessor
{
    /**
     * @param $rawResponse
     * @return \Paranoia\Transfer\Response\ResponseInterface
     */
    public function process($rawResponse)
    {
        $response = new SaleResponse();
        $this->prepareResponse($rawResponse, $response);
        return $response;
    }
}