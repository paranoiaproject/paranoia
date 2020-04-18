<?php
namespace Paranoia\Core\ResponseParser;

use Paranoia\Core\Response\AuthorizationResponse;
use Psr\Http\Message\ResponseInterface;

interface AuthorizationResponseParser
{
    public function parse(ResponseInterface $response): AuthorizationResponse;
}
