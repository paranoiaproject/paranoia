<?php
namespace Paranoia\Core\ResponseParser;

use Paranoia\Core\Response\ChargeResponse;
use Psr\Http\Message\ResponseInterface;

interface ChargeResponseParser
{
    public function parse(ResponseInterface $response): ChargeResponse;
}
