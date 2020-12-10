<?php
namespace Paranoia\Test\Unit;

use Paranoia\Client;
use Paranoia\Core\Acquirer\AcquirerAdapter;
use Paranoia\Core\Acquirer\Service\AuthorizationService;
use Paranoia\Core\Acquirer\Service\CancelService;
use Paranoia\Core\Acquirer\Service\CaptureService;
use Paranoia\Core\Acquirer\Service\ChargeService;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use Paranoia\Core\Acquirer\Service\RefundService;
use Paranoia\Core\Model\Request\AuthorizationRequest;
use Paranoia\Core\Model\Request\CancelRequest;
use Paranoia\Core\Model\Request\CaptureRequest;
use Paranoia\Core\Model\Request\ChargeRequest;
use Paranoia\Core\Model\Request\RefundRequest;
use Paranoia\Core\Model\Response\AuthorizationResponse;
use Paranoia\Core\Model\Response\CancelResponse;
use Paranoia\Core\Model\Response\CaptureResponse;
use Paranoia\Core\Model\Response\ChargeResponse;
use Paranoia\Core\Model\Response\RefundResponse;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    /** @var Client */
    private $client;

    /** @var \PHPUnit_Framework_MockObject_MockObject | AcquirerAdapter */
    private $acquirerMock;

    public function setup() {
        $this->acquirerMock = $this->getMockBuilder(AcquirerAdapter::class)->getMock();
        $this->client = new Client($this->acquirerMock);
    }

    public function testAuthorize()
    {
        /** @var AuthorizationRequest | \PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->getMockBuilder(AuthorizationRequest::class)->getMock();
        $responseMock = $this->getMockBuilder(AuthorizationResponse::class)->getMock();

        $serviceMock = $this->getMockBuilder(AuthorizationService::class)->getMock();

        $serviceMock->expects($this->once())
            ->method('process')
            ->with($requestMock)
            ->will($this->returnValue($responseMock));

        $serviceFactoryMock = $this->getMockBuilder(AbstractServiceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serviceFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($serviceMock));

        $this->acquirerMock->expects($this->once())
            ->method('getServiceFactory')
            ->with(AbstractServiceFactory::AUTHORIZATION)
            ->will($this->returnValue($serviceFactoryMock));

        $response = $this->client->authorize($requestMock);
        $this->assertEquals($responseMock, $response, 'Unexpected authorization response');
    }

    public function testCapture()
    {
        /** @var CaptureRequest | \PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->getMockBuilder(CaptureRequest::class)->getMock();
        $responseMock = $this->getMockBuilder(CaptureResponse::class)->getMock();

        $serviceMock = $this->getMockBuilder(CaptureService::class)->getMock();

        $serviceMock->expects($this->once())
            ->method('process')
            ->with($requestMock)
            ->will($this->returnValue($responseMock));

        $serviceFactoryMock = $this->getMockBuilder(AbstractServiceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serviceFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($serviceMock));

        $this->acquirerMock->expects($this->once())
            ->method('getServiceFactory')
            ->with(AbstractServiceFactory::CAPTURE)
            ->will($this->returnValue($serviceFactoryMock));

        $response = $this->client->capture($requestMock);
        $this->assertEquals($responseMock, $response, 'Unexpected capture response');
    }

    public function testCharge()
    {
        /** @var ChargeRequest | \PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->getMockBuilder(ChargeRequest::class)->getMock();
        $responseMock = $this->getMockBuilder(ChargeResponse::class)->getMock();

        $serviceMock = $this->getMockBuilder(ChargeService::class)->getMock();

        $serviceMock->expects($this->once())
            ->method('process')
            ->with($requestMock)
            ->will($this->returnValue($responseMock));

        $serviceFactoryMock = $this->getMockBuilder(AbstractServiceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serviceFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($serviceMock));

        $this->acquirerMock->expects($this->once())
            ->method('getServiceFactory')
            ->with(AbstractServiceFactory::CHARGE)
            ->will($this->returnValue($serviceFactoryMock));

        $response = $this->client->charge($requestMock);
        $this->assertEquals($responseMock, $response, 'Unexpected charge response');
    }

    public function testRefund()
    {
        /** @var RefundRequest | \PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->getMockBuilder(RefundRequest::class)->getMock();
        $responseMock = $this->getMockBuilder(RefundResponse::class)->getMock();

        $serviceMock = $this->getMockBuilder(RefundService::class)->getMock();

        $serviceMock->expects($this->once())
            ->method('process')
            ->with($requestMock)
            ->will($this->returnValue($responseMock));

        $serviceFactoryMock = $this->getMockBuilder(AbstractServiceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serviceFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($serviceMock));

        $this->acquirerMock->expects($this->once())
            ->method('getServiceFactory')
            ->with(AbstractServiceFactory::REFUND)
            ->will($this->returnValue($serviceFactoryMock));

        $response = $this->client->refund($requestMock);
        $this->assertEquals($responseMock, $response, 'Unexpected refund response');
    }

    public function testCancel()
    {
        /** @var CancelRequest | \PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->getMockBuilder(CancelRequest::class)->getMock();
        $responseMock = $this->getMockBuilder(CancelResponse::class)->getMock();

        $serviceMock = $this->getMockBuilder(CancelService::class)->getMock();

        $serviceMock->expects($this->once())
            ->method('process')
            ->with($requestMock)
            ->will($this->returnValue($responseMock));

        $serviceFactoryMock = $this->getMockBuilder(AbstractServiceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serviceFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($serviceMock));

        $this->acquirerMock->expects($this->once())
            ->method('getServiceFactory')
            ->with(AbstractServiceFactory::CANCEL)
            ->will($this->returnValue($serviceFactoryMock));

        $response = $this->client->cancel($requestMock);
        $this->assertEquals($responseMock, $response, 'Unexpected cancel response');
    }
}
