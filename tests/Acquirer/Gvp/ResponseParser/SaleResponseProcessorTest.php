<?php
namespace Paranoia\Test\Acquirer\Gvp\ResponseParser;

use Paranoia\Core\AbstractConfiguration;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Acquirer\Gvp\ResponseParser\SaleResponseParser;
use Paranoia\Core\Model\Response;
use PHPUnit\Framework\TestCase;

class SaleResponseProcessorTest extends TestCase
{
    public function test_success_response()
    {
        $rawResponse = file_get_contents(
            __DIR__ . '/../../../samples/response/gvp/sale_successful.xml'
        );

        /** @var AbstractConfiguration $configuration */
        $configuration = $this->getMockBuilder(AbstractConfiguration::class)->getMock();
        $processor = new SaleResponseParser($configuration);
        $response = $processor->process($rawResponse);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(true, $response->isSuccess());
        $this->assertEquals('311710676028', $response->getTransactionId());
        $this->assertEquals('245093', $response->getAuthCode());
        $this->assertEquals('SISTC157B93B81C74BB88C6CA1C15D0CA338', $response->getOrderId());
    }

    public function test_failed_response()
    {
        $rawResponse = file_get_contents(
            __DIR__ . '/../../../samples/response/gvp/sale_failed.xml'
        );

        /** @var AbstractConfiguration $configuration */
        $configuration = $this->getMockBuilder(AbstractConfiguration::class)->getMock();
        $processor = new SaleResponseParser($configuration);
        $response = $processor->process($rawResponse);
        $this->assertInstanceOf(Response::class, $response);
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
        $processor = new SaleResponseParser($configuration);

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
