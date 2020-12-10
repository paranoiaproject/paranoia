<?php
namespace Paranoia\Test\Unit\Acquirer\NestPay\Service\Factory;

use Paranoia\Acquirer\NestPay\NestPayConfiguration;
use Paranoia\Acquirer\NestPay\Service\Factory\RefundServiceFactory;
use Paranoia\Acquirer\NestPay\Service\RefundServiceImp;
use Paranoia\Core\Acquirer\Service\RefundService;
use PHPUnit\Framework\TestCase;

class RefundServiceFactoryTest extends TestCase
{
    public function testCreate()
    {
        /** @var NestPayConfiguration | \PHPUnit_Framework_MockObject_MockObject $configurationMock */
        $configurationMock = $this->getMockBuilder(NestPayConfiguration::class)->getMock();
        $serviceFactory = new RefundServiceFactory($configurationMock);
        $serviceImp = $serviceFactory->create();
        $this->assertInstanceOf(RefundServiceImp::class, $serviceImp);
        $this->assertTrue($serviceImp instanceof RefundService);
    }
}
