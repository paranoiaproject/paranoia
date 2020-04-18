<?php
namespace Paranoia\Core\RequestBuilder;

use Paranoia\Core\Request\RefundRequest;

interface RefundRequestBuilder
{
    public function build(RefundRequest $request): array;
}
