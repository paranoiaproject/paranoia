<?php
namespace Paranoia\Processor\NestPay;

use Paranoia\Transfer\Response\PreAuthorizationResponse;

class PreAuthorizationResponseProcessor extends AbstractNestPayProcessor
{
    /**
     * @param $rawResponse
     * @return \Paranoia\Transfer\Response\ResponseInterface
     */
    public function process($rawResponse)
    {
        $response = new PreAuthorizationResponse();
        $this->prepareResponse($rawResponse, $response);
        return $response;
    }
}