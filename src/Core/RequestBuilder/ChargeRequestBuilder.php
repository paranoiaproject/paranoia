<?php
namespace Paranoia\Core\RequestBuilder;

use Paranoia\Core\Request\ChargeRequest;

interface ChargeRequestBuilder
{
    public function build(ChargeRequest $request): array;
}
