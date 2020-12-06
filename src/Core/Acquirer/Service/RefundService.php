<?php
namespace Paranoia\Core\Acquirer\Service;

use Paranoia\Core\Model\Request\RefundRequest;
use Paranoia\Core\Model\Response\RefundResponse;

/**
 * Interface RefundService
 * @package Paranoia\Core\Acquirer\Service
 */
interface RefundService
{
    /**
     * @param RefundRequest $request
     * @return RefundResponse
     */
    public function process(RefundRequest $request): RefundResponse;
}