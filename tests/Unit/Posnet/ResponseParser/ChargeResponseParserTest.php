<?php
namespace Paranoia\Unit\Test\Posnet\ResponseParser;

use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\UnapprovedTransactionException;
use Paranoia\Core\Transformer\XmlTransformer;
use Paranoia\Posnet\ResponseParser\ChargeResponseParser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ChargeResponseParserTest extends TestCase
{
    public function test_parse_with_successful_message()
    {
        $content = \file_get_contents(__DIR__ . '/../../../stub/posnet/response/charge_successful.xml');

        /** @var ResponseInterface $responseMock */
        $responseMock = $this->getResponseMock($content);

        /** @var XmlTransformer $transformerMock */
        $transformerMock = $this->getXmlTransformerMock($content);

        $parser = new ChargeResponseParser($transformerMock);
        $chargeResponse = $parser->parse($responseMock);
        $this->assertEquals('007912', $chargeResponse->getAuthCode());
        $this->assertEquals('0001000004P0503281', $chargeResponse->getTransactionId());
    }

    public function test_parse_with_failed_message()
    {
        $content = \file_get_contents(__DIR__ . '/../../../stub/posnet/response/charge_failed.xml');
        /** @var ResponseInterface $responseMock */
        $responseMock = $this->getResponseMock($content);

        /** @var XmlTransformer $transformerMock */
        $transformerMock = $this->getXmlTransformerMock($content);

        $parser = new ChargeResponseParser($transformerMock);
        try {
            $parser->parse($responseMock);
        } catch (BadResponseException $e) {

        } catch (UnapprovedTransactionException $e) {
            $this->assertEquals('0225', $e->getErrorCode());
            $this->assertEquals('ONAYLANMADI:0225 ISL. YAPILAMIY 0225', $e->getMessage());
            $this->assertEquals('', $e->getDetails());
        }
    }

    public function test_parse_with_unexpected_response()
    {
        $this->expectException(BadResponseException::class);

        /** @var ResponseInterface $responseMock */
        $content = '/* unexpected-server-response */';
        $responseMock = $this->getResponseMock($content);

        /** @var XmlTransformer $transformerMock */
        $transformerMock = $this->getXmlTransformerMock($content, true);
        $parser = new ChargeResponseParser($transformerMock);
        $parser->parse($responseMock);
    }

    /**
     * @param string $content
     * @return MockObject
     */
    public function getResponseMock(string $content): MockObject
    {
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $response->expects($this->once())->method('getBody')->willReturn($content);
        return $response;
    }

    /**
     * @param string $content
     * @param bool $throwError
     * @return MockObject
     */
    public function getXmlTransformerMock(string $content, bool $throwError=false): MockObject
    {
        $transformerMock = $this->getMockBuilder(XmlTransformer::class)->getMock();
        $method = $transformerMock
            ->expects($this->once())
            ->method('transform')->with($content);

        if ($throwError) {
            $method->willThrowException(new BadResponseException('Unexpected response'));
        } else {
            $method->willReturn(new \SimpleXMLElement($content));
        }

        return $transformerMock;
    }
}
