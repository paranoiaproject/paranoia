<?php
namespace Paranoia\Core\RequestBuilder;

use Paranoia\Core\Request\CaptureRequest;

interface CaptureRequestBuilder
{
    public function build(CaptureRequest $request): array;
}
