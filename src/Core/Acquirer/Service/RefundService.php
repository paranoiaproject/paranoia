<?php
namespace Paranoia\Core\Acquirer\Service;

use Paranoia\Core\Model\Request\RefundRequest;
use Paranoia\Core\Model\Response\RefundResponse;

interface RefundService
{
    public function process(RefundRequest $request): RefundResponse;
}