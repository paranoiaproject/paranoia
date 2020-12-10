<?php

namespace Paranoia\Test\Unit\Acquirer\Posnet\Service;

use Paranoia\Acquirer\Posnet\RequestBuilder\CaptureRequestBuilder;
use Paranoia\Acquirer\Posnet\ResponseParser\CaptureResponseParser;
use Paranoia\Acquirer\Posnet\Service\CaptureServiceImp;
use Paranoia\Core\Model\Request\CaptureRequest;
use Paranoia\Core\Model\Request\HttpRequest;
use Paranoia\Core\Model\Response\CaptureResponse;
use Paranoia\Lib\HttpClient;
use PHPUnit\Framework\TestCase;

class CaptureServiceImpTest extends TestCase
{
    /** @var CaptureRequestBuilder | \PHPUnit_Framework_MockObject_MockObject */
    private $requestBuilderMock;

    /** @var CaptureResponseParser | \PHPUnit_Framework_MockObject_MockObject */
    private $responseParserMock;

    /** @var HttpClient | \PHPUnit_Framework_MockObject_MockObject */
    private $httpClientMock;

    /** @var CaptureServiceImp */
    private $service;

    public function setup()
    {
        $this->requestBuilderMock = $this->getMockBuilder(CaptureRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();


        $this->responseParserMock = $this->getMockBuilder(CaptureResponseParser::class)
            ->disableOriginalConstructor()
            ->getMock();


        $this->httpClientMock = $this->getMockBuilder(HttpClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new CaptureServiceImp(
            $this->requestBuilderMock,
            $this->responseParserMock,
            $this->httpClientMock
        );
    }

    public function testProcess()
    {
        /** @var CaptureRequest | \PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->getMockBuilder(CaptureRequest::class)->getMock();

        $httpRequestMock = $this->getMockBuilder(HttpRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock = $this->getMockBuilder(CaptureResponse::class)->getMock();

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
