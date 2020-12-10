<?php
namespace Paranoia\Test\Unit\Acquirer\Posnet\Service\Factory;

use Paranoia\Acquirer\Posnet\PosnetConfiguration;
use Paranoia\Acquirer\Posnet\Service\Factory\CancelServiceFactory;
use Paranoia\Acquirer\Posnet\Service\CancelServiceImp;
use Paranoia\Core\Acquirer\Service\CancelService;
use PHPUnit\Framework\TestCase;

class CancelServiceFactoryTest extends TestCase
{
    public function testCreate()
    {
        /** @var PosnetConfiguration | \PHPUnit_Framework_MockObject_MockObject $configurationMock */
        $configurationMock = $this->getMockBuilder(PosnetConfiguration::class)->getMock();
        $serviceFactory = new CancelServiceFactory($configurationMock);
        $serviceImp = $serviceFactory->create();
        $this->assertInstanceOf(CancelServiceImp::class, $serviceImp);
        $this->assertTrue($serviceImp instanceof CancelService);
    }
}
