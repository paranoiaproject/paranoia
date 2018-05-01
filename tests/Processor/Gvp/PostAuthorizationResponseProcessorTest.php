<?php
namespace Paranoia\Test\Processor\Gvp;

use Paranoia\Configuration\AbstractConfiguration;
use Paranoia\Exception\BadResponseException;
use Paranoia\Processor\Gvp\PostAuthorizationResponseProcessor;
use Paranoia\Response\PaymentResponse;
use PHPUnit\Framework\TestCase;

class PostAuthorizationResponseProcessorTest extends TestCase
{
    public function test_success_response()
    {
        $rawResponse = file_get_contents(
            __DIR__ . '/../../samples/response/gvp/post_authorization_successful.xml'
        );

        /** @var AbstractConfiguration $configuration */
        $configuration = $this->getMockBuilder(AbstractConfiguration::class)->getMock();
        $processor = new PostAuthorizationResponseProcessor($configuration);
        $response = $processor->process($rawResponse);
        $this->assertInstanceOf(PaymentResponse::class, $response);
        $this->assertEquals(true, $response->isSuccess());
        $this->assertEquals('105809652539', $response->getTransactionId());
        $this->assertEquals('914729', $response->getAuthCode());
        $this->assertEquals('SIST2E8748F43EA24754912E365D637B91D8', $response->getOrderId());
    }

    public function test_failed_response()
    {
        $rawResponse = file_get_contents(
            __DIR__ . '/../../samples/response/gvp/post_authorization_failed.xml'
        );

        /** @var AbstractConfiguration $configuration */
        $configuration = $this->getMockBuilder(AbstractConfiguration::class)->getMock();
        $processor = new PostAuthorizationResponseProcessor($configuration);
        $response = $processor->process($rawResponse);
        $this->assertInstanceOf(PaymentResponse::class, $response);
        $this->assertEquals(false, $response->isSuccess());
        $this->assertEquals(null, $response->getTransactionId());
        $this->assertEquals(null, $response->getOrderId());
        $this->assertEquals('54', $response->getResponseCode());
        $this->assertEquals('Error Message: ›˛leminizi gerÁekle˛tiremiyoruz.Tekrar deneyiniz System Error Message: SON KULLANMA TARIHI HATALI', $response->getResponseMessage());
    }

    /**
     * @dataProvider badResponses
     * @param string $rawResponse
     */
    public function test_bad_response($rawResponse)
    {
        /** @var AbstractConfiguration $configuration */
        $configuration = $this->getMockBuilder(AbstractConfiguration::class)->getMock();
        $processor = new PostAuthorizationResponseProcessor($configuration);

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
