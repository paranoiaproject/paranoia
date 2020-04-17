<?php
namespace Paranoia\Test\Unit\Nestpay\RequestBuilder;

use Paranoia\Configuration\NestpayConfiguration;
use Paranoia\Core\Request\CancelRequest;
use Paranoia\Nestpay\RequestBuilder\CancelRequestBuilder;
use PHPUnit\Framework\TestCase;

class CancelRequestBuilderTest extends TestCase
{
    public function dataProvider(): array
    {
        // TODO: Test both are empty case. It must throw validation error
        return [
            ['0000000001', null, __DIR__ . '/../../../stub/nestpay/request/cancel_with_orderId.xml'],
            [null, '0000000002', __DIR__ . '/../../../stub/nestpay/request/cancel_with_transId.xml'],
        ];
    }

    /**
     * @param string|null $orderId
     * @param string|null $transactionId
     * @param string $expectedXmlFilename
     * @dataProvider dataProvider
     */
    public function test_build(?string $orderId, ?string $transactionId, string $expectedXmlFilename):void
    {
        $configuration = $this->getConfiguration();
        $requestBuilder = $this->getRequestBuilder($configuration);
        $request = $this->getRequest($orderId, $transactionId);

        $providerRequest = $requestBuilder->build($request);

        $formParamKey = array_shift(array_keys($providerRequest));
        $formParamValue = array_shift(array_values($providerRequest));
        $this->assertEquals('DATA', $formParamKey);
        $this->assertXmlStringEqualsXmlFile(
            $expectedXmlFilename,
            $formParamValue
        );
    }

    /**
     * @return NestpayConfiguration
     */
    public function getConfiguration(): NestpayConfiguration
    {
        $configuration = new NestpayConfiguration();
        $configuration->setClientId('000001');
        $configuration->setUsername('NESTPAYUSER');
        $configuration->setPassword('NESTPAYPASS');
        return $configuration;
    }

    /**
     * @param NestpayConfiguration $configuration
     * @return CancelRequestBuilder
     */
    public function getRequestBuilder(NestpayConfiguration $configuration): CancelRequestBuilder
    {
        return new CancelRequestBuilder($configuration);
    }

    /**
     * @param string $orderId
     * @param string $transactionId
     * @return CancelRequest
     */
    public function getRequest(?string $orderId, ?string $transactionId): CancelRequest
    {
        $request = new CancelRequest();
        $request->setOrderId($orderId);
        $request->setTransactionId($transactionId);
        return $request;
    }
}
