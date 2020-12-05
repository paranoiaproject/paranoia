<?php
namespace Paranoia\Core\Acquirer\Service;

use Paranoia\Core\Model\Request\ChargeRequest;
use Paranoia\Core\Model\Response\ChargeResponse;

/**
 * Interface ChargeService
 * @package Paranoia\Core\Acquirer\Service
 */
interface ChargeService
{
    /**
     * @param ChargeRequest $request
     * @return ChargeResponse
     */
    public function process(ChargeRequest $request): ChargeResponse;
}