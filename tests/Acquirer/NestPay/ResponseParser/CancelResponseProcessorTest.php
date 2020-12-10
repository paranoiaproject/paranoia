<?php
namespace Paranoia\Test\Acquirer\NestPay\ResponseParser;

use Paranoia\Acquirer\NestPay\ResponseParser\CancelResponseParser;
use Paranoia\Core\Acquirer\BaseConfiguration;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Model\Response;
use PHPUnit\Framework\TestCase;

class CancelResponseProcessorTest extends TestCase
{
    public function test_success_response()
    {
        $rawResponse = file_get_contents(
            __DIR__ . '/../../../samples/response/nestpay/cancel_successful.xml'
        );

        /** @var BaseConfiguration $configuration */
        $configuration = $this->getMockBuilder(BaseConfiguration::class)->getMock();
        $processor = new CancelResponseParser($configuration);
        $response = $processor->parse($rawResponse);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(true, $response->isSuccess());
        $this->assertEquals('15335I94G07024820', $response->getTransactionId());
        $this->assertEquals('PB6356', $response->getAuthCode());
        $this->assertEquals('133577162970961', $response->getOrderId());
    }

    public function test_failed_response()
    {
        $rawResponse = file_get_contents(
            __DIR__ . '/../../../samples/response/nestpay/cancel_failed.xml'
        );

        /** @var BaseConfiguration $configuration */
        $configuration = $this->getMockBuilder(BaseConfiguration::class)->getMock();
        $processor = new CancelResponseParser($configuration);
        $response = $processor->parse($rawResponse);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(false, $response->isSuccess());
        $this->assertEquals(null, $response->getTransactionId());
        $this->assertEquals(null, $response->getOrderId());
        $this->assertEquals('05', $response->getResponseCode());
        $this->assertEquals('Error Message: Genel red  Host Message: [RC 05] Red Onaylanmadı - Do Not Honour', $response->getResponseMessage());
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
