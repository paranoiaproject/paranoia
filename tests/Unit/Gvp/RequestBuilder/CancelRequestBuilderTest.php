<?php
namespace Paranoia\Test\Unit\Gvp\RequestBuilder;

use Paranoia\Configuration\GvpConfiguration;
use Paranoia\Core\Request\CancelRequest;
use Paranoia\Gvp\RequestBuilder\CancelRequestBuilder;
use PHPUnit\Framework\TestCase;

class CancelRequestBuilderTest extends TestCase
{
    public function dataProvider(): array
    {
        // TODO: Garanti doesn't work with only transaction ID like Nestpay and Posnet. Find a common way.
        return [
            ['0000000001', null, __DIR__ . '/../../../stub/gvp/request/cancel_with_orderId.xml'],
            ['0000000001', '0000000002', __DIR__ . '/../../../stub/gvp/request/cancel_with_transactionId.xml'],
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
        $this->assertEquals('data', $formParamKey);
        $this->assertXmlStringEqualsXmlFile(
            $expectedXmlFilename,
            $formParamValue
        );
    }

    /**
     * @return GvpConfiguration
     */
    public function getConfiguration(): GvpConfiguration
    {
        $configuration = new GvpConfiguration();
        $configuration->setAuthorizationUsername('PROVAUT');
        $configuration->setAuthorizationPassword('PROVAUT');
        $configuration->setRefundUsername('PROVRFN');
        $configuration->setRefundPassword('PROVRFN');
        $configuration->setMerchantId('123456');
        $configuration->setTerminalId('654321');
        $configuration->setMode('TEST');
        return $configuration;
    }

    /**
     * @param GvpConfiguration $configuration
     * @return CancelRequestBuilder
     */
    public function getRequestBuilder(GvpConfiguration $configuration): CancelRequestBuilder
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
