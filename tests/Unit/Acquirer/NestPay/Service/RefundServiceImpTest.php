<?php

namespace Paranoia\Test\Unit\Acquirer\NestPay\Service;

use Paranoia\Acquirer\NestPay\RequestBuilder\RefundRequestBuilder;
use Paranoia\Acquirer\NestPay\ResponseParser\RefundResponseParser;
use Paranoia\Acquirer\NestPay\Service\RefundServiceImp;
use Paranoia\Core\Model\Request\RefundRequest;
use Paranoia\Core\Model\Request\HttpRequest;
use Paranoia\Core\Model\Response\RefundResponse;
use Paranoia\Lib\HttpClient;
use PHPUnit\Framework\TestCase;

class RefundServiceImpTest extends TestCase
{
    /** @var RefundRequestBuilder | \PHPUnit_Framework_MockObject_MockObject */
    private $requestBuilderMock;

    /** @var RefundResponseParser | \PHPUnit_Framework_MockObject_MockObject */
    private $responseParserMock;

    /** @var HttpClient | \PHPUnit_Framework_MockObject_MockObject */
    private $httpClientMock;

    /** @var RefundServiceImp */
    private $service;

    public function setup()
    {
        $this->requestBuilderMock = $this->getMockBuilder(RefundRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();


        $this->responseParserMock = $this->getMockBuilder(RefundResponseParser::class)
            ->disableOriginalConstructor()
            ->getMock();


        $this->httpClientMock = $this->getMockBuilder(HttpClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new RefundServiceImp(
            $this->requestBuilderMock,
            $this->responseParserMock,
            $this->httpClientMock
        );
    }

    public function testProcess()
    {
        /** @var RefundRequest | \PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->getMockBuilder(RefundRequest::class)->getMock();

        $httpRequestMock = $this->getMockBuilder(HttpRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock = $this->getMockBuilder(RefundResponse::class)->getMock();

        $this->requestBuilderMock->expects($this->once())
            ->method('build')
            ->with($requestMock)
            ->willReturn($httpRequestMock);

        $this->responseParserMock->expects($this->once())
            ->method('parse')
            ->with('DUMMY-HTTP-RESPONSE-BODY')
            ->will($this->returnValue($responseMock));

        $this->httpClientMock->expects($this->once())
            ->method('send')
            ->with($httpRequestMock)
            ->will($this->returnValue('DUMMY-HTTP-RESPONSE-BODY'));

        $response = $this->service->process($requestMock);

        $this->assertEquals($responseMock, $response);
    }
}
