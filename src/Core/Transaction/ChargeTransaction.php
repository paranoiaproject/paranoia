<?php
namespace Paranoia\Core\Transaction;

use Paranoia\Core\Request\ChargeRequest;
use Paranoia\Core\Response\ChargeResponse;

interface ChargeTransaction
{
    /**
     * @param ChargeRequest $request
     * @return ChargeResponse
     */
    public function perform(ChargeRequest $request): ChargeResponse;
}
