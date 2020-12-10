<?php
namespace Paranoia\Test\Unit\Acquirer\Posnet\Service\Factory;

use Paranoia\Acquirer\Posnet\PosnetConfiguration;
use Paranoia\Acquirer\Posnet\Service\Factory\RefundServiceFactory;
use Paranoia\Acquirer\Posnet\Service\RefundServiceImp;
use Paranoia\Core\Acquirer\Service\RefundService;
use PHPUnit\Framework\TestCase;

class RefundServiceFactoryTest extends TestCase
{
    public function testCreate()
    {
        /** @var PosnetConfiguration | \PHPUnit_Framework_MockObject_MockObject $configurationMock */
        $configurationMock = $this->getMockBuilder(PosnetConfiguration::class)->getMock();
        $serviceFactory = new RefundServiceFactory($configurationMock);
        $serviceImp = $serviceFactory->create();
        $this->assertInstanceOf(RefundServiceImp::class, $serviceImp);
        $this->assertTrue($serviceImp instanceof RefundService);
    }
}
