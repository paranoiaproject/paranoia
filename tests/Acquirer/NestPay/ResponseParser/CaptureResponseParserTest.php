<?php
namespace Paranoia\Test\Acquirer\NestPay\ResponseParser;

use Paranoia\Acquirer\NestPay\ResponseParser\CaptureResponseParser;
use Paranoia\Core\AbstractConfiguration;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Model\Response;
use PHPUnit\Framework\TestCase;

class CaptureResponseParserTest extends TestCase
{
    public function test_success_response()
    {
        $rawResponse = file_get_contents(
            __DIR__ . '/../../../samples/response/nestpay/post_authorization_successful.xml'
        );

        /** @var AbstractConfiguration $configuration */
        $configuration = $this->getMockBuilder(AbstractConfiguration::class)->getMock();
        $processor = new CaptureResponseParser($configuration);
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
            __DIR__ . '/../../../samples/response/nestpay/post_authorization_failed.xml'
        );

        /** @var AbstractConfiguration $configuration */
        $configuration = $this->getMockBuilder(AbstractConfiguration::class)->getMock();
        $processor = new CaptureResponseParser($configuration);
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
        /** @var AbstractConfiguration $configuration */
        $configuration = $this->getMockBuilder(AbstractConfiguration::class)->getMock();
        $processor = new CaptureResponseParser($configuration);

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
