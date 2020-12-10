<?php
namespace Paranoia\Test\Unit\Acquirer\NestPay\Service\Factory;

use Paranoia\Acquirer\NestPay\NestPayConfiguration;
use Paranoia\Acquirer\NestPay\Service\Factory\CancelServiceFactory;
use Paranoia\Acquirer\NestPay\Service\CancelServiceImp;
use Paranoia\Core\Acquirer\Service\CancelService;
use PHPUnit\Framework\TestCase;

class CancelServiceFactoryTest extends TestCase
{
    public function testCreate()
    {
        /** @var NestPayConfiguration | \PHPUnit_Framework_MockObject_MockObject $configurationMock */
        $configurationMock = $this->getMockBuilder(NestPayConfiguration::class)->getMock();
        $serviceFactory = new CancelServiceFactory($configurationMock);
        $serviceImp = $serviceFactory->create();
        $this->assertInstanceOf(CancelServiceImp::class, $serviceImp);
        $this->assertTrue($serviceImp instanceof CancelService);
    }
}
