<?php
namespace Paranoia\Test\Acquirer\Gvp\ResponseParser;

use Paranoia\Acquirer\Gvp\ResponseParser\AuthorizationResponseParser;
use Paranoia\Core\Acquirer\BaseConfiguration;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Model\Response;
use PHPUnit\Framework\TestCase;

class AuthorizationResponseParserTest extends TestCase
{
    public function test_success_response()
    {
        $rawResponse = file_get_contents(
            __DIR__ . '/../../../samples/response/gvp/pre_authorization_successful.xml'
        );

        /** @var BaseConfiguration $configuration */
        $configuration = $this->getMockBuilder(BaseConfiguration::class)->getMock();
        $processor = new AuthorizationResponseParser($configuration);
        $response = $processor->parse($rawResponse);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(true, $response->isSuccess());
        $this->assertEquals('311710676052', $response->getTransactionId());
        $this->assertEquals('412290', $response->getAuthCode());
        $this->assertEquals('SISTFA03907C0DB14A38B3BA380722891160', $response->getOrderId());
    }

    public function test_failed_response()
    {
        $rawResponse = file_get_contents(
            __DIR__ . '/../../../samples/response/gvp/pre_authorization_failed.xml'
        );

        /** @var BaseConfiguration $configuration */
        $configuration = $this->getMockBuilder(BaseConfiguration::class)->getMock();
        $processor = new AuthorizationResponseParser($configuration);
        $response = $processor->parse($rawResponse);
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
        /** @var BaseConfiguration $configuration */
        $configuration = $this->getMockBuilder(BaseConfiguration::class)->getMock();
        $processor = new AuthorizationResponseParser($configuration);

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
