<?php
namespace Paranoia\Nestpay\ResponseParser;

use Paranoia\Core\Response\AuthorizationResponse;
use Psr\Http\Message\ResponseInterface;

class AuthorizationResponseParser
{
    public function parse(ResponseInterface $response): AuthorizationResponse
    {
        try {
            $xml = new \SimpleXMLElement($response->getBody());
        } catch (\Exception $exception) {

        }
    }
}
