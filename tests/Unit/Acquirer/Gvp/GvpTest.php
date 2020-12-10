<?php

namespace Paranoia\Test\Unit\Acquirer\Gvp;

use Paranoia\Acquirer\Gvp\Gvp;
use Paranoia\Acquirer\Gvp\GvpConfiguration;
use Paranoia\Acquirer\Gvp\Service\Factory\AuthorizationServiceFactory;
use Paranoia\Acquirer\Gvp\Service\Factory\CancelServiceFactory;
use Paranoia\Acquirer\Gvp\Service\Factory\CaptureServiceFactory;
use Paranoia\Acquirer\Gvp\Service\Factory\ChargeServiceFactory;
use Paranoia\Acquirer\Gvp\Service\Factory\RefundServiceFactory;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use PHPUnit\Framework\TestCase;

class GvpTest extends TestCase
{
    public function paymentActionDataProvider(): array
    {
        return [
            [AbstractServiceFactory::AUTHORIZATION, AuthorizationServiceFactory::class],
            [AbstractServiceFactory::CAPTURE, CaptureServiceFactory::class],
            [AbstractServiceFactory::CHARGE, ChargeServiceFactory::class],
            [AbstractServiceFactory::REFUND, RefundServiceFactory::class],
            [AbstractServiceFactory::CANCEL, CancelServiceFactory::class]
        ];
    }

    /**
     * @param string $paymentAction
     * @param string $expectedServiceFactory
     * @dataProvider paymentActionDataProvider
     */
    public function testGetServiceFactory(string $paymentAction, string $expectedServiceFactory): void
    {
        /** @var GvpConfiguration | \PHPUnit_Framework_MockObject_MockObject $configurationMock */
        $configurationMock = $this->getMockBuilder(GvpConfiguration::class)->getMock();
        $acquirerAdapter = new Gvp($configurationMock);
        $serviceFactory = $acquirerAdapter->getServiceFactory($paymentAction);
        $this->assertInstanceOf($expectedServiceFactory, $serviceFactory, 'Unexpected service factory');
    }
}
