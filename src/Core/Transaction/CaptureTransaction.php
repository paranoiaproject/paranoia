<?php
namespace Paranoia\Core\Transaction;

use Paranoia\Core\Request\CaptureRequest;
use Paranoia\Core\Response\CaptureResponse;

interface CaptureTransaction
{
    /**
     * @param CaptureRequest $request
     * @return CaptureResponse
     */
    public function perform(CaptureRequest $request): CaptureResponse;
}
