<?php

namespace Paranoia\Test\Unit\Nestpay;

use Paranoia\Configuration\NestpayConfiguration;
use Paranoia\Core\ProviderContext;
use Paranoia\Nestpay\NestpayContextFactory;
use PHPUnit\Framework\TestCase;

class NestpayContextFactoryTest extends TestCase
{
    public function test_createContext(): void
    {
        $configurationStub = $this->createStub(NestpayConfiguration::class);
        $factory = new NestpayContextFactory($configurationStub);
        $this->assertInstanceOf(ProviderContext::class, $factory->createContext());
    }
}
