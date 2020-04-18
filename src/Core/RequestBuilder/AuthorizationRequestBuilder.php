<?php
namespace Paranoia\Core\RequestBuilder;

use Paranoia\Core\Request\AuthorizationRequest;

interface AuthorizationRequestBuilder
{
    public function build(AuthorizationRequest $request): array;
}
