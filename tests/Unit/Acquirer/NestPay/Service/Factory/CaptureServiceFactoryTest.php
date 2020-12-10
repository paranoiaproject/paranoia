<?php
namespace Paranoia\Test\Unit\Acquirer\NestPay\Service\Factory;

use Paranoia\Acquirer\NestPay\NestPayConfiguration;
use Paranoia\Acquirer\NestPay\Service\CaptureServiceImp;
use Paranoia\Acquirer\NestPay\Service\Factory\CaptureServiceFactory;
use Paranoia\Core\Acquirer\Service\CaptureService;
use PHPUnit\Framework\TestCase;

class CaptureServiceFactoryTest extends TestCase
{
    public function testCreate()
    {
        /** @var NestPayConfiguration | \PHPUnit_Framework_MockObject_MockObject $configurationMock */
        $configurationMock = $this->getMockBuilder(NestPayConfiguration::class)->getMock();
        $serviceFactory = new CaptureServiceFactory($configurationMock);
        $serviceImp = $serviceFactory->create();
        $this->assertInstanceOf(CaptureServiceImp::class, $serviceImp);
        $this->assertTrue($serviceImp instanceof CaptureService);
    }
}
