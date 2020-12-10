<?php
namespace Paranoia\Test\Unit\Acquirer\Gvp\Service\Factory;

use Paranoia\Acquirer\Gvp\GvpConfiguration;
use Paranoia\Acquirer\Gvp\Service\Factory\RefundServiceFactory;
use Paranoia\Acquirer\Gvp\Service\RefundServiceImp;
use Paranoia\Core\Acquirer\Service\RefundService;
use PHPUnit\Framework\TestCase;

class RefundServiceFactoryTest extends TestCase
{
    public function testCreate()
    {
        /** @var GvpConfiguration | \PHPUnit_Framework_MockObject_MockObject $configurationMock */
        $configurationMock = $this->getMockBuilder(GvpConfiguration::class)->getMock();
        $serviceFactory = new RefundServiceFactory($configurationMock);
        $serviceImp = $serviceFactory->create();
        $this->assertInstanceOf(RefundServiceImp::class, $serviceImp);
        $this->assertTrue($serviceImp instanceof RefundService);
    }
}
