<?php

namespace Paranoia\Test\Unit\Acquirer\NestPay\Service;

use Paranoia\Acquirer\NestPay\RequestBuilder\AuthorizationRequestBuilder;
use Paranoia\Acquirer\NestPay\ResponseParser\AuthorizationResponseParser;
use Paranoia\Acquirer\NestPay\Service\AuthorizationServiceImp;
use Paranoia\Core\Model\Request\AuthorizationRequest;
use Paranoia\Core\Model\Request\HttpRequest;
use Paranoia\Core\Model\Response\AuthorizationResponse;
use Paranoia\Lib\HttpClient;
use PHPUnit\Framework\TestCase;

class AuthorizationServiceImpTest extends TestCase
{
    /** @var AuthorizationRequestBuilder | \PHPUnit_Framework_MockObject_MockObject */
    private $requestBuilderMock;

    /** @var AuthorizationResponseParser | \PHPUnit_Framework_MockObject_MockObject */
    private $responseParserMock;

    /** @var HttpClient | \PHPUnit_Framework_MockObject_MockObject */
    private $httpClientMock;

    /** @var AuthorizationServiceImp */
    private $service;

    public function setup()
    {
        $this->requestBuilderMock = $this->getMockBuilder(AuthorizationRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();


        $this->responseParserMock = $this->getMockBuilder(AuthorizationResponseParser::class)
            ->disableOriginalConstructor()
            ->getMock();


        $this->httpClientMock = $this->getMockBuilder(HttpClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new AuthorizationServiceImp(
            $this->requestBuilderMock,
            $this->responseParserMock,
            $this->httpClientMock
        );
    }

    public function testProcess()
    {
        /** @var AuthorizationRequest | \PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->getMockBuilder(AuthorizationRequest::class)->getMock();

        $httpRequestMock = $this->getMockBuilder(HttpRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock = $this->getMockBuilder(AuthorizationResponse::class)->getMock();

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
