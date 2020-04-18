<?php
namespace Paranoia\Core\Transaction;

use Paranoia\Core\Request\AuthorizationRequest;
use Paranoia\Core\Response\AuthorizationResponse;

interface AuthorizationTransaction
{
    /**
     * @param AuthorizationRequest $request
     * @return AuthorizationResponse
     */
    public function perform(AuthorizationRequest $request): AuthorizationResponse;
}
