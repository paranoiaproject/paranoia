<?php
namespace Paranoia\Acquirer\NestPay;

use Paranoia\Acquirer\NestPay\Service\Factory\AuthorizationServiceFactory;
use Paranoia\Acquirer\NestPay\Service\Factory\CancelServiceFactory;
use Paranoia\Acquirer\NestPay\Service\Factory\CaptureServiceFactory;
use Paranoia\Acquirer\NestPay\Service\Factory\ChargeServiceFactory;
use Paranoia\Acquirer\NestPay\Service\Factory\RefundServiceFactory;
use Paranoia\Core\Acquirer\AcquirerAdapter;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use Paranoia\Core\Exception\InvalidArgumentException;

/**
 * Class NestPay
 * @package Paranoia\Acquirer\NestPay
 */
class NestPay implements AcquirerAdapter
{
    /** @var NestPayConfiguration */
    private $configuration;

    /**
     * NestPay constructor.
     * @param NestPayConfiguration $configuration
     */
    public function __construct(NestPayConfiguration $configuration)
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
