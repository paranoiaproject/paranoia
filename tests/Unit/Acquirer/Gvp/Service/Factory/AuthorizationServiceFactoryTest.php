<?php
namespace Paranoia\Test\Unit\Acquirer\Gvp\Service\Factory;

use Paranoia\Acquirer\Gvp\GvpConfiguration;
use Paranoia\Acquirer\Gvp\Service\AuthorizationServiceImp;
use Paranoia\Acquirer\Gvp\Service\Factory\AuthorizationServiceFactory;
use Paranoia\Core\Acquirer\Service\AuthorizationService;
use PHPUnit\Framework\TestCase;

class AuthorizationServiceFactoryTest extends TestCase
{
    public function testCreate()
    {
        /** @var GvpConfiguration | \PHPUnit_Framework_MockObject_MockObject $configurationMock */
        $configurationMock = $this->getMockBuilder(GvpConfiguration::class)->getMock();
        $serviceFactory = new AuthorizationServiceFactory($configurationMock);
        $serviceImp = $serviceFactory->create();
        $this->assertInstanceOf(AuthorizationServiceImp::class, $serviceImp);
        $this->assertTrue($serviceImp instanceof AuthorizationService);
    }
}
