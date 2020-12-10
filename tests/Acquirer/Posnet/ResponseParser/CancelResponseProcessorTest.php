<?php
namespace Paranoia\Test\Acquirer\Posnet\ResponseParser;

use Paranoia\Acquirer\Posnet\ResponseParser\CancelResponseParser;
use Paranoia\Core\Acquirer\BaseConfiguration;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Model\Response;
use PHPUnit\Framework\TestCase;

class CancelResponseProcessorTest extends TestCase
{
    public function test_success_response()
    {
        $rawResponse = file_get_contents(
            __DIR__ . '/../../../samples/response/posnet/cancel_successful.xml'
        );

        /** @var BaseConfiguration $configuration */
        $configuration = $this->getMockBuilder(BaseConfiguration::class)->getMock();
        $processor = new CancelResponseParser($configuration);
        $response = $processor->parse($rawResponse);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(true, $response->isSuccess());
        $this->assertEquals('0001000009P0503281', $response->getTransactionId());
        $this->assertEquals('000000', $response->getAuthCode());
        $this->assertEquals(null, $response->getOrderId());
    }

    public function test_failed_response()
    {
        $rawResponse = file_get_contents(
            __DIR__ . '/../../../samples/response/posnet/cancel_failed.xml'
        );

        /** @var BaseConfiguration $configuration */
        $configuration = $this->getMockBuilder(BaseConfiguration::class)->getMock();
        $processor = new CancelResponseParser($configuration);
        $response = $processor->parse($rawResponse);
        $this->assertInstanceOf(Response::class, $response);
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
        /** @var BaseConfiguration $configuration */
        $configuration = $this->getMockBuilder(BaseConfiguration::class)->getMock();
        $processor = new CancelResponseParser($configuration);

        $this->expectException(BadResponseException::class);
        $processor->parse($rawResponse);
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
