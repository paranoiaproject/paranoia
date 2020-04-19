<?php
namespace Paranoia\Test\Unit\Posnet\RequestBuilder;

use Paranoia\Configuration\PosnetConfiguration;
use Paranoia\Core\Request\CancelRequest;
use Paranoia\Posnet\RequestBuilder\CancelRequestBuilder;
use PHPUnit\Framework\TestCase;

class CancelRequestBuilderTest extends TestCase
{
    public function dataProvider(): array
    {
        // TODO: Posnet recommend to work with hostlogref which means transaction identified instead of order identifier
        // So we ignored OrderId option in posnet.
        return [
//            ['0000000001', null, __DIR__ . '/../../../stub/posnet/request/cancel_with_orderId.xml'],
            [null, '0000000002', __DIR__ . '/../../../stub/posnet/request/cancel_with_transId.xml'],
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
        $this->assertEquals('xmldata', $formParamKey);
        $this->assertXmlStringEqualsXmlFile(
            $expectedXmlFilename,
            $formParamValue
        );
    }

    /**
     * @return PosnetConfiguration
     */
    public function getConfiguration(): PosnetConfiguration
    {
        $configuration = new PosnetConfiguration();
        $configuration->setApiUrl('http://example.com');
        $configuration->setTerminalId('1000000002');
        $configuration->setMerchantId('3000040005');
        $configuration->setUsername('POSNETUSER');
        $configuration->setPassword('POSNETPASS');
        return $configuration;
    }

    /**
     * @param PosnetConfiguration $configuration
     * @return CancelRequestBuilder
     */
    public function getRequestBuilder(PosnetConfiguration $configuration): CancelRequestBuilder
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
