<?php
namespace Paranoia\Core\Acquirer\Service;

use Paranoia\Core\Model\Request\CancelRequest;
use Paranoia\Core\Model\Response\CancelResponse;

/**
 * Interface CancelService
 * @package Paranoia\Core\Acquirer\Service
 */
interface CancelService
{
    /**
     * @param CancelRequest $request
     * @return CancelResponse
     */
    public function process(CancelRequest $request): CancelResponse;
}