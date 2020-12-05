<?php
namespace Paranoia\Core\Acquirer;

use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;

/**
 * Interface AcquirerAdapter
 * @package Paranoia\Core\Acquirer
 */
interface AcquirerAdapter
{
    /**
     * @param string $serviceType
     * @return AbstractServiceFactory
     */
    public function getServiceFactory(string $serviceType): AbstractServiceFactory;
}