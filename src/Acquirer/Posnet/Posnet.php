<?php
namespace Paranoia\Acquirer\Posnet;

use Paranoia\Acquirer\Posnet\Service\Factory\AuthorizationServiceFactory;
use Paranoia\Acquirer\Posnet\Service\Factory\CancelServiceFactory;
use Paranoia\Acquirer\Posnet\Service\Factory\CaptureServiceFactory;
use Paranoia\Acquirer\Posnet\Service\Factory\ChargeServiceFactory;
use Paranoia\Acquirer\Posnet\Service\Factory\RefundServiceFactory;
use Paranoia\Core\Acquirer\AcquirerAdapter;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use Paranoia\Core\Exception\InvalidArgumentException;

/**
 * Class Posnet
 * @package Paranoia\Acquirer\Posnet
 */
class Posnet implements AcquirerAdapter
{
    /** @var PosnetConfiguration */
    private $configuration;

    /**
     * Posnet constructor.
     * @param PosnetConfiguration $configuration
     */
    public function __construct(PosnetConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }


    /**
     * @param string $serviceType
     * @return AbstractServiceFactory
     */
    public function getServiceFactory(string $serviceType): AbstractServiceFactory
    {
        switch ($serviceType) {
            case AbstractServiceFactory::AUTHORIZATION:
                return new AuthorizationServiceFactory($this->configuration);
            case AbstractServiceFactory::CAPTURE:
                return new CaptureServiceFactory($this->configuration);
            case AbstractServiceFactory::CHARGE:
                return new ChargeServiceFactory($this->configuration);
            case AbstractServiceFactory::REFUND:
                return new RefundServiceFactory($this->configuration);
            case AbstractServiceFactory::CANCEL:
                return new CancelServiceFactory($this->configuration);
            default:
                throw new InvalidArgumentException('Unknown service type');
        }
    }
}
