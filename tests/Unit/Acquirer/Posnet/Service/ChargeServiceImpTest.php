<?php

namespace Paranoia\Test\Unit\Acquirer\Posnet\Service;

use Paranoia\Acquirer\Posnet\RequestBuilder\ChargeRequestBuilder;
use Paranoia\Acquirer\Posnet\ResponseParser\ChargeResponseParser;
use Paranoia\Acquirer\Posnet\Service\ChargeServiceImp;
use Paranoia\Core\Model\Request\ChargeRequest;
use Paranoia\Core\Model\Request\HttpRequest;
use Paranoia\Core\Model\Response\ChargeResponse;
use Paranoia\Lib\HttpClient;
use PHPUnit\Framework\TestCase;

class ChargeServiceImpTest extends TestCase
{
    /** @var ChargeRequestBuilder | \PHPUnit_Framework_MockObject_MockObject */
    private $requestBuilderMock;

    /** @var ChargeResponseParser | \PHPUnit_Framework_MockObject_MockObject */
    private $responseParserMock;

    /** @var HttpClient | \PHPUnit_Framework_MockObject_MockObject */
    private $httpClientMock;

    /** @var ChargeServiceImp */
    private $service;

    public function setup()
    {
        $this->requestBuilderMock = $this->getMockBuilder(ChargeRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();


        $this->responseParserMock = $this->getMockBuilder(ChargeResponseParser::class)
            ->disableOriginalConstructor()
            ->getMock();


        $this->httpClientMock = $this->getMockBuilder(HttpClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new ChargeServiceImp(
            $this->requestBuilderMock,
            $this->responseParserMock,
            $this->httpClientMock
        );
    }

    public function testProcess()
    {
        /** @var ChargeRequest | \PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->getMockBuilder(ChargeRequest::class)->getMock();

        $httpRequestMock = $this->getMockBuilder(HttpRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock = $this->getMockBuilder(ChargeResponse::class)->getMock();

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
