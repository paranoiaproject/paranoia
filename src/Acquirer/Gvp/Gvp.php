<?php
namespace Paranoia\Acquirer\Gvp;

use Paranoia\Acquirer\Gvp\Service\Factory\AuthorizationServiceFactory;
use Paranoia\Acquirer\Gvp\Service\Factory\CancelServiceFactory;
use Paranoia\Acquirer\Gvp\Service\Factory\CaptureServiceFactory;
use Paranoia\Acquirer\Gvp\Service\Factory\ChargeServiceFactory;
use Paranoia\Acquirer\Gvp\Service\Factory\RefundServiceFactory;
use Paranoia\Core\Acquirer\AcquirerAdapter;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use Paranoia\Core\Exception\InvalidArgumentException;

/**
 * Class Gvp
 * @package Paranoia\Acquirer\Gvp
 */
class Gvp implements AcquirerAdapter
{
    /** @var GvpConfiguration */
    private $configuration;

    /**
     * Gvp constructor.
     * @param GvpConfiguration $configuration
     */
    public function __construct(GvpConfiguration $configuration)
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
