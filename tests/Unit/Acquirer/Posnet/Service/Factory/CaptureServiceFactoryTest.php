<?php
namespace Paranoia\Test\Unit\Acquirer\Posnet\Service\Factory;

use Paranoia\Acquirer\Posnet\PosnetConfiguration;
use Paranoia\Acquirer\Posnet\Service\CaptureServiceImp;
use Paranoia\Acquirer\Posnet\Service\Factory\CaptureServiceFactory;
use Paranoia\Core\Acquirer\Service\CaptureService;
use PHPUnit\Framework\TestCase;

class CaptureServiceFactoryTest extends TestCase
{
    public function testCreate()
    {
        /** @var PosnetConfiguration | \PHPUnit_Framework_MockObject_MockObject $configurationMock */
        $configurationMock = $this->getMockBuilder(PosnetConfiguration::class)->getMock();
        $serviceFactory = new CaptureServiceFactory($configurationMock);
        $serviceImp = $serviceFactory->create();
        $this->assertInstanceOf(CaptureServiceImp::class, $serviceImp);
        $this->assertTrue($serviceImp instanceof CaptureService);
    }
}
