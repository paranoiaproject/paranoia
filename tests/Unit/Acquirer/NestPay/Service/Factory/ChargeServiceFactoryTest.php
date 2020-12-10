<?php
namespace Paranoia\Test\Unit\Acquirer\NestPay\Service\Factory;

use Paranoia\Acquirer\NestPay\NestPayConfiguration;
use Paranoia\Acquirer\NestPay\Service\ChargeServiceImp;
use Paranoia\Acquirer\NestPay\Service\Factory\ChargeServiceFactory;
use Paranoia\Core\Acquirer\Service\ChargeService;
use PHPUnit\Framework\TestCase;

class ChargeServiceFactoryTest extends TestCase
{
    public function testCreate()
    {
        /** @var NestPayConfiguration | \PHPUnit_Framework_MockObject_MockObject $configurationMock */
        $configurationMock = $this->getMockBuilder(NestPayConfiguration::class)->getMock();
        $serviceFactory = new ChargeServiceFactory($configurationMock);
        $serviceImp = $serviceFactory->create();
        $this->assertInstanceOf(ChargeServiceImp::class, $serviceImp);
        $this->assertTrue($serviceImp instanceof ChargeService);
    }
}
