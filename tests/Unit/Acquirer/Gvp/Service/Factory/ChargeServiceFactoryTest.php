<?php
namespace Paranoia\Test\Unit\Acquirer\Gvp\Service\Factory;

use Paranoia\Acquirer\Gvp\GvpConfiguration;
use Paranoia\Acquirer\Gvp\Service\ChargeServiceImp;
use Paranoia\Acquirer\Gvp\Service\Factory\ChargeServiceFactory;
use Paranoia\Core\Acquirer\Service\ChargeService;
use PHPUnit\Framework\TestCase;

class ChargeServiceFactoryTest extends TestCase
{
    public function testCreate()
    {
        /** @var GvpConfiguration | \PHPUnit_Framework_MockObject_MockObject $configurationMock */
        $configurationMock = $this->getMockBuilder(GvpConfiguration::class)->getMock();
        $serviceFactory = new ChargeServiceFactory($configurationMock);
        $serviceImp = $serviceFactory->create();
        $this->assertInstanceOf(ChargeServiceImp::class, $serviceImp);
        $this->assertTrue($serviceImp instanceof ChargeService);
    }
}
