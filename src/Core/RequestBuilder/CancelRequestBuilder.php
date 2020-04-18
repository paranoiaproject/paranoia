<?php
namespace Paranoia\Core\RequestBuilder;

use Paranoia\Core\Request\CancelRequest;

interface CancelRequestBuilder
{
    public function build(CancelRequest $request): array;
}
