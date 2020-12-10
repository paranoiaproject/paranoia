<?php
namespace Paranoia\Test\Unit\Acquirer\Posnet\Service\Factory;

use Paranoia\Acquirer\Posnet\PosnetConfiguration;
use Paranoia\Acquirer\Posnet\Service\ChargeServiceImp;
use Paranoia\Acquirer\Posnet\Service\Factory\ChargeServiceFactory;
use Paranoia\Core\Acquirer\Service\ChargeService;
use PHPUnit\Framework\TestCase;

class ChargeServiceFactoryTest extends TestCase
{
    public function testCreate()
    {
        /** @var PosnetConfiguration | \PHPUnit_Framework_MockObject_MockObject $configurationMock */
        $configurationMock = $this->getMockBuilder(PosnetConfiguration::class)->getMock();
        $serviceFactory = new ChargeServiceFactory($configurationMock);
        $serviceImp = $serviceFactory->create();
        $this->assertInstanceOf(ChargeServiceImp::class, $serviceImp);
        $this->assertTrue($serviceImp instanceof ChargeService);
    }
}
