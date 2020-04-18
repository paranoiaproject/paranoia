<?php
namespace Paranoia\Core\ResponseParser;

use Paranoia\Core\Response\CancelResponse;
use Psr\Http\Message\ResponseInterface;

interface CancelResponseParser
{
    public function parse(ResponseInterface $response): CancelResponse;
}
