<?php
namespace Paranoia\Processor\NestPay;

use Paranoia\Transfer\Response\PostAuthorizationResponse;

class PostAuthorizationResponseProcessor extends AbstractNestPayProcessor
{
    /**
     * @param $rawResponse
     * @return \Paranoia\Transfer\Response\ResponseInterface
     */
    public function process($rawResponse)
    {
        $response = new PostAuthorizationResponse();
        $this->prepareResponse($rawResponse, $response);
        return $response;
    }
}