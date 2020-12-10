<?php
namespace Paranoia\Test\Unit\Acquirer\Posnet;

use Paranoia\Acquirer\Posnet\Service\Factory\AuthorizationServiceFactory;
use Paranoia\Acquirer\Posnet\Service\Factory\CancelServiceFactory;
use Paranoia\Acquirer\Posnet\Service\Factory\CaptureServiceFactory;
use Paranoia\Acquirer\Posnet\Service\Factory\ChargeServiceFactory;
use Paranoia\Acquirer\Posnet\Service\Factory\RefundServiceFactory;
use Paranoia\Acquirer\Posnet\Posnet;
use Paranoia\Acquirer\Posnet\PosnetConfiguration;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use PHPUnit\Framework\TestCase;

class PosnetTest extends TestCase
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
        /** @var PosnetConfiguration | \PHPUnit_Framework_MockObject_MockObject $configurationMock */
        $configurationMock = $this->getMockBuilder(PosnetConfiguration::class)->getMock();
        $acquirerAdapter = new Posnet($configurationMock);
        $serviceFactory = $acquirerAdapter->getServiceFactory($paymentAction);
        $this->assertInstanceOf($expectedServiceFactory, $serviceFactory, 'Unexpected service factory');
    }
}
