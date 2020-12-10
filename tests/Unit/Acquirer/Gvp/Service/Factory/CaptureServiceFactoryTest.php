<?php
namespace Paranoia\Test\Unit\Acquirer\Gvp\Service\Factory;

use Paranoia\Acquirer\Gvp\GvpConfiguration;
use Paranoia\Acquirer\Gvp\Service\CaptureServiceImp;
use Paranoia\Acquirer\Gvp\Service\Factory\CaptureServiceFactory;
use Paranoia\Core\Acquirer\Service\CaptureService;
use PHPUnit\Framework\TestCase;

class CaptureServiceFactoryTest extends TestCase
{
    public function testCreate()
    {
        /** @var GvpConfiguration | \PHPUnit_Framework_MockObject_MockObject $configurationMock */
        $configurationMock = $this->getMockBuilder(GvpConfiguration::class)->getMock();
        $serviceFactory = new CaptureServiceFactory($configurationMock);
        $serviceImp = $serviceFactory->create();
        $this->assertInstanceOf(CaptureServiceImp::class, $serviceImp);
        $this->assertTrue($serviceImp instanceof CaptureService);
    }
}
