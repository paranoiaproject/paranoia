<?php
namespace Paranoia\Test\Processor\Posnet;

use Paranoia\Configuration\AbstractConfiguration;
use Paranoia\Exception\BadResponseException;
use Paranoia\Processor\Posnet\CancelResponseProcessor;
use Paranoia\Response\PaymentResponse;
use PHPUnit\Framework\TestCase;

class CancelResponseProcessorTest extends TestCase
{
    public function test_success_response()
    {
        $rawResponse = file_get_contents(
            __DIR__ . '/../../samples/response/posnet/cancel_successful.xml'
        );

        /** @var AbstractConfiguration $configuration */
        $configuration = $this->getMockBuilder(AbstractConfiguration::class)->getMock();
        $processor = new CancelResponseProcessor($configuration);
        $response = $processor->process($rawResponse);
        $this->assertInstanceOf(PaymentResponse::class, $response);
        $this->assertEquals(true, $response->isSuccess());
        $this->assertEquals('0001000009P0503281', $response->getTransactionId());
        $this->assertEquals('000000', $response->getAuthCode());
        $this->assertEquals(null, $response->getOrderId());
    }

    public function test_failed_response()
    {
        $rawResponse = file_get_contents(
            __DIR__ . '/../../samples/response/posnet/cancel_failed.xml'
        );

        /** @var AbstractConfiguration $configuration */
        $configuration = $this->getMockBuilder(AbstractConfiguration::class)->getMock();
        $processor = new CancelResponseProcessor($configuration);
        $response = $processor->process($rawResponse);
        $this->assertInstanceOf(PaymentResponse::class, $response);
        $this->assertEquals(false, $response->isSuccess());
        $this->assertEquals(null, $response->getTransactionId());
        $this->assertEquals(null, $response->getOrderId());
        $this->assertEquals('0225', $response->getResponseCode());
        $this->assertEquals('ONAYLANMADI:0225 ISL. YAPILAMIY 0225 ', $response->getResponseMessage());
    }

    /**
     * @dataProvider badResponses
     * @param string $rawResponse
     */
    public function test_bad_response($rawResponse)
    {
        /** @var AbstractConfiguration $configuration */
        $configuration = $this->getMockBuilder(AbstractConfiguration::class)->getMock();
        $processor = new CancelResponseProcessor($configuration);

        $this->expectException(BadResponseException::class);
        $processor->process($rawResponse);
    }

    public function badResponses()
    {
        return [
            [null],
            [''],
            ['DUMMY'],
            [1],
        ];
    }

}
