<?php
namespace Paranoia\Test\Unit\Acquirer\NestPay\Service\Factory;

use Paranoia\Acquirer\NestPay\NestPayConfiguration;
use Paranoia\Acquirer\NestPay\Service\AuthorizationServiceImp;
use Paranoia\Acquirer\NestPay\Service\Factory\AuthorizationServiceFactory;
use Paranoia\Core\Acquirer\Service\AuthorizationService;
use PHPUnit\Framework\TestCase;

class AuthorizationServiceFactoryTest extends TestCase
{
    public function testCreate()
    {
        /** @var NestPayConfiguration | \PHPUnit_Framework_MockObject_MockObject $configurationMock */
        $configurationMock = $this->getMockBuilder(NestPayConfiguration::class)->getMock();
        $serviceFactory = new AuthorizationServiceFactory($configurationMock);
        $serviceImp = $serviceFactory->create();
        $this->assertInstanceOf(AuthorizationServiceImp::class, $serviceImp);
        $this->assertTrue($serviceImp instanceof AuthorizationService);
    }
}
