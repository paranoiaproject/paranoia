<?php
namespace Paranoia\Test\Unit\Acquirer\Gvp\Service\Factory;

use Paranoia\Acquirer\Gvp\GvpConfiguration;
use Paranoia\Acquirer\Gvp\Service\Factory\CancelServiceFactory;
use Paranoia\Acquirer\Gvp\Service\CancelServiceImp;
use Paranoia\Core\Acquirer\Service\CancelService;
use PHPUnit\Framework\TestCase;

class CancelServiceFactoryTest extends TestCase
{
    public function testCreate()
    {
        /** @var GvpConfiguration | \PHPUnit_Framework_MockObject_MockObject $configurationMock */
        $configurationMock = $this->getMockBuilder(GvpConfiguration::class)->getMock();
        $serviceFactory = new CancelServiceFactory($configurationMock);
        $serviceImp = $serviceFactory->create();
        $this->assertInstanceOf(CancelServiceImp::class, $serviceImp);
        $this->assertTrue($serviceImp instanceof CancelService);
    }
}
