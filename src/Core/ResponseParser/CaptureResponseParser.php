<?php
namespace Paranoia\Core\ResponseParser;

use Paranoia\Core\Response\CaptureResponse;
use Psr\Http\Message\ResponseInterface;

interface CaptureResponseParser
{
    public function parse(ResponseInterface $response): CaptureResponse;
}
