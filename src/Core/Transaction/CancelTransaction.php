<?php
namespace Paranoia\Core\Transaction;

use Paranoia\Core\Request\CancelRequest;
use Paranoia\Core\Response\CancelResponse;

interface CancelTransaction
{
    /**
     * @param CancelRequest $request
     * @return CancelResponse
     */
    public function perform(CancelRequest $request): CancelResponse;
}
