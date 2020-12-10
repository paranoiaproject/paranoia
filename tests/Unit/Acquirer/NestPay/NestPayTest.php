<?php
namespace Paranoia\Test\Unit\Acquirer\NestPay;

use Paranoia\Acquirer\NestPay\NestPay;
use Paranoia\Acquirer\NestPay\NestPayConfiguration;
use Paranoia\Acquirer\NestPay\Service\Factory\AuthorizationServiceFactory;
use Paranoia\Acquirer\NestPay\Service\Factory\CancelServiceFactory;
use Paranoia\Acquirer\NestPay\Service\Factory\CaptureServiceFactory;
use Paranoia\Acquirer\NestPay\Service\Factory\ChargeServiceFactory;
use Paranoia\Acquirer\NestPay\Service\Factory\RefundServiceFactory;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;

class NestPayTest extends \PHPUnit_Framework_TestCase
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
        /** @var NestPayConfiguration | \PHPUnit_Framework_MockObject_MockObject $configurationMock */
        $configurationMock = $this->getMockBuilder(NestPayConfiguration::class)->getMock();
        $acquirerAdapter = new NestPay($configurationMock);
        $serviceFactory = $acquirerAdapter->getServiceFactory($paymentAction);
        $this->assertInstanceOf($expectedServiceFactory, $serviceFactory, 'Unexpected service factory');
    }
}
