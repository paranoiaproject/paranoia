<?php

namespace Paranoia\Test\Unit\Acquirer\NestPay\Service;

use Paranoia\Acquirer\NestPay\RequestBuilder\CancelRequestBuilder;
use Paranoia\Acquirer\NestPay\ResponseParser\CancelResponseParser;
use Paranoia\Acquirer\NestPay\Service\CancelServiceImp;
use Paranoia\Core\Model\Request\CancelRequest;
use Paranoia\Core\Model\Request\HttpRequest;
use Paranoia\Core\Model\Response\CancelResponse;
use Paranoia\Lib\HttpClient;
use PHPUnit\Framework\TestCase;

class CancelServiceImpTest extends TestCase
{
    /** @var CancelRequestBuilder | \PHPUnit_Framework_MockObject_MockObject */
    private $requestBuilderMock;

    /** @var CancelResponseParser | \PHPUnit_Framework_MockObject_MockObject */
    private $responseParserMock;

    /** @var HttpClient | \PHPUnit_Framework_MockObject_MockObject */
    private $httpClientMock;

    /** @var CancelServiceImp */
    private $service;

    public function setup()
    {
        $this->requestBuilderMock = $this->getMockBuilder(CancelRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();


        $this->responseParserMock = $this->getMockBuilder(CancelResponseParser::class)
            ->disableOriginalConstructor()
            ->getMock();


        $this->httpClientMock = $this->getMockBuilder(HttpClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new CancelServiceImp(
            $this->requestBuilderMock,
            $this->responseParserMock,
            $this->httpClientMock
        );
    }

    public function testProcess()
    {
        /** @var CancelRequest | \PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->getMockBuilder(CancelRequest::class)->getMock();

        $httpRequestMock = $this->getMockBuilder(HttpRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock = $this->getMockBuilder(CancelResponse::class)->getMock();

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
