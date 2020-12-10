<?php
namespace Paranoia\Test\Unit\Acquirer\Posnet\Service\Factory;

use Paranoia\Acquirer\Posnet\PosnetConfiguration;
use Paranoia\Acquirer\Posnet\Service\AuthorizationServiceImp;
use Paranoia\Acquirer\Posnet\Service\Factory\AuthorizationServiceFactory;
use Paranoia\Core\Acquirer\Service\AuthorizationService;
use PHPUnit\Framework\TestCase;

class AuthorizationServiceFactoryTest extends TestCase
{
    public function testCreate()
    {
        /** @var PosnetConfiguration | \PHPUnit_Framework_MockObject_MockObject $configurationMock */
        $configurationMock = $this->getMockBuilder(PosnetConfiguration::class)->getMock();
        $serviceFactory = new AuthorizationServiceFactory($configurationMock);
        $serviceImp = $serviceFactory->create();
        $this->assertInstanceOf(AuthorizationServiceImp::class, $serviceImp);
        $this->assertTrue($serviceImp instanceof AuthorizationService);
    }
}
