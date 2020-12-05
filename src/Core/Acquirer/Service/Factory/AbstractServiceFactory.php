<?php
namespace Paranoia\Core\Acquirer\Service\Factory;

use Paranoia\Core\Acquirer\Service\AuthorizationService;
use Paranoia\Core\Acquirer\Service\CancelService;
use Paranoia\Core\Acquirer\Service\CaptureService;
use Paranoia\Core\Acquirer\Service\ChargeService;
use Paranoia\Core\Acquirer\Service\RefundService;

abstract class AbstractServiceFactory
{
    public const AUTHORIZATION = 'authorization';
    public const CAPTURE = 'capture';
    public const CHARGE = 'charge';
    public const REFUND = 'refund';
    public const CANCEL = 'cancel';

    /**
     * @TODO: I wish php would support generic type someday.
     * @return AuthorizationService | CaptureService | ChargeService | RefundService | CancelService
     */
    abstract public function create();
}