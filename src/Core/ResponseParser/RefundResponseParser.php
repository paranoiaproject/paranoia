<?php
namespace Paranoia\Core\ResponseParser;

use Paranoia\Core\Response\RefundResponse;
use Psr\Http\Message\ResponseInterface;

interface RefundResponseParser
{
    public function parse(ResponseInterface $response): RefundResponse;
}
