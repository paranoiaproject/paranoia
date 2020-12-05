<?php
namespace Paranoia\Core\Acquirer\Service;

use Paranoia\Core\Model\Request\CaptureRequest;
use Paranoia\Core\Model\Response\CaptureResponse;

/**
 * Interface CaptureService
 * @package Paranoia\Core\Acquirer\Service
 */
interface CaptureService
{
    /**
     * @param CaptureRequest $request
     * @return CaptureResponse
     */
    public function process(CaptureRequest $request): CaptureResponse;
}