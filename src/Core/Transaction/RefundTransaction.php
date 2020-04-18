<?php
namespace Paranoia\Core\Transaction;

use Paranoia\Core\Request\RefundRequest;
use Paranoia\Core\Response\RefundResponse;

interface RefundTransaction
{
    /**
     * @param RefundRequest $request
     * @return RefundResponse
     */
    public function perform(RefundRequest $request): RefundResponse;
}
