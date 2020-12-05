<?php
namespace Paranoia\Core\Acquirer\Service;

use Paranoia\Core\Model\Request\AuthorizationRequest;
use Paranoia\Core\Model\Response\AuthorizationResponse;

/**
 * Interface AuthorizationService
 * @package Paranoia\Core\Acquirer\Service
 */
interface AuthorizationService
{
    /**
     * @param AuthorizationRequest $request
     * @return AuthorizationResponse
     */
    public function process(AuthorizationRequest $request): AuthorizationResponse;
}