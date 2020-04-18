<?php
namespace Paranoia\Test\Unit\Nestpay\Transaction;

use GuzzleHttp\Client;
use Paranoia\Configuration\NestpayConfiguration;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\CommunicationError;
use Paranoia\Core\Exception\UnapprovedTransactionException;
use Paranoia\Core\Request\AuthorizationRequest;
use Paranoia\Core\Request\CancelRequest;
use Paranoia\Core\Request\CaptureRequest;
use Paranoia\Core\Request\ChargeRequest;
use Paranoia\Core\Request\RefundRequest;
use Paranoia\Core\Response\AuthorizationResponse;
use Paranoia\Core\Response\CancelResponse;
use Paranoia\Core\Response\CaptureResponse;
use Paranoia\Core\Response\ChargeResponse;
use Paranoia\Core\Response\RefundResponse;
use Paranoia\Nestpay\RequestBuilder\AuthorizationRequestBuilder;
use Paranoia\Nestpay\RequestBuilder\CancelRequestBuilder;
use Paranoia\Nestpay\RequestBuilder\CaptureRequestBuilder;
use Paranoia\Nestpay\RequestBuilder\ChargeRequestBuilder;
use Paranoia\Nestpay\RequestBuilder\RefundRequestBuilder;
use Paranoia\Nestpay\ResponseParser\AuthorizationResponseParser;
use Paranoia\Nestpay\ResponseParser\CancelResponseParser;
use Paranoia\Nestpay\ResponseParser\CaptureResponseParser;
use Paranoia\Nestpay\ResponseParser\ChargeResponseParser;
use Paranoia\Nestpay\ResponseParser\RefundResponseParser;
use Paranoia\Nestpay\Transaction\AuthorizationTransaction;
use Paranoia\Nestpay\Transaction\CancelTransaction;
use Paranoia\Nestpay\Transaction\CaptureTransaction;
use Paranoia\Nestpay\Transaction\ChargeTransaction;
use Paranoia\Nestpay\Transaction\RefundTransaction;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class TransactionTest extends TestCase
{
    public function successFlowDataProvider(): array
    {
        return [
            [
                AuthorizationRequest::class,
                AuthorizationResponse::class,
                AuthorizationRequestBuilder::class,
                AuthorizationResponseParser::class,
                AuthorizationTransaction::class,
            ],
            [
                CaptureRequest::class,
                CaptureResponse::class,
                CaptureRequestBuilder::class,
                CaptureResponseParser::class,
                CaptureTransaction::class,
            ],
            [
                ChargeRequest::class,
                ChargeResponse::class,
                ChargeRequestBuilder::class,
                ChargeResponseParser::class,
                ChargeTransaction::class,
            ],
            [
                CancelRequest::class,
                CancelResponse::class,
                CancelRequestBuilder::class,
                CancelResponseParser::class,
                CancelTransaction::class,
            ],
            [
                RefundRequest::class,
                RefundResponse::class,
                RefundRequestBuilder::class,
                RefundResponseParser::class,
                RefundTransaction::class,
            ],
        ];
    }

    public function responseParserErrorProvider(): array
    {
        return [
            [
                AuthorizationRequest::class,
                AuthorizationRequestBuilder::class,
                AuthorizationResponseParser::class,
                BadResponseException::class,
                AuthorizationTransaction::class,
            ],
            [
                AuthorizationRequest::class,
                AuthorizationRequestBuilder::class,
                AuthorizationResponseParser::class,
                UnapprovedTransactionException::class,
                AuthorizationTransaction::class,
            ],
            [
                CaptureRequest::class,
                CaptureRequestBuilder::class,
                CaptureResponseParser::class,
                BadResponseException::class,
                CaptureTransaction::class,
            ],
            [
                CaptureRequest::class,
                CaptureRequestBuilder::class,
                CaptureResponseParser::class,
                UnapprovedTransactionException::class,
                CaptureTransaction::class,
            ],
            [
                ChargeRequest::class,
                ChargeRequestBuilder::class,
                ChargeResponseParser::class,
                BadResponseException::class,
                ChargeTransaction::class,
            ],
            [
                ChargeRequest::class,
                ChargeRequestBuilder::class,
                ChargeResponseParser::class,
                UnapprovedTransactionException::class,
                ChargeTransaction::class,
            ],
            [
                CancelRequest::class,
                CancelRequestBuilder::class,
                CancelResponseParser::class,
                BadResponseException::class,
                CancelTransaction::class,
            ],
            [
                CancelRequest::class,
                CancelRequestBuilder::class,
                CancelResponseParser::class,
                UnapprovedTransactionException::class,
                CancelTransaction::class,
            ],
            [
                RefundRequest::class,
                RefundRequestBuilder::class,
                RefundResponseParser::class,
                BadResponseException::class,
                RefundTransaction::class,
            ],
            [
                RefundRequest::class,
                RefundRequestBuilder::class,
                RefundResponseParser::class,
                UnapprovedTransactionException::class,
                RefundTransaction::class,
            ],
        ];
    }

    public function httpClientErrorProvider(): array
    {
        return [
            [
                AuthorizationRequest::class,
                AuthorizationRequestBuilder::class,
                AuthorizationResponseParser::class,
                CommunicationError::class,
                AuthorizationTransaction::class,
            ],
            [
                CaptureRequest::class,
                CaptureRequestBuilder::class,
                CaptureResponseParser::class,
                CommunicationError::class,
                CaptureTransaction::class,
            ],
            [
                ChargeRequest::class,
                ChargeRequestBuilder::class,
                ChargeResponseParser::class,
                CommunicationError::class,
                ChargeTransaction::class,
            ],
            [
                CancelRequest::class,
                CancelRequestBuilder::class,
                CancelResponseParser::class,
                CommunicationError::class,
                CancelTransaction::class,
            ],
            [
                RefundRequest::class,
                RefundRequestBuilder::class,
                RefundResponseParser::class,
                CommunicationError::class,
                RefundTransaction::class,
            ],
        ];
    }

    /**
     * @param string $requestType
     * @param string $responseType
     * @param string $requestBuilderType
     * @param string $responseParserType
     * @param string $transactionType
     * @dataProvider successFlowDataProvider
     */
    public function test_perform(
        string $requestType,
        string $responseType,
        string $requestBuilderType,
        string $responseParserType,
        string $transactionType
    ) {
        $configuration = $this->getConfiguration();

        $requestStub = $this->createStub($requestType);
        $responseStub = $this->createStub($responseType);
        $providerRequestStub = ['field' => 'expected-value'];
        $httpClientResponseStub = $this->createStub(ResponseInterface::class);

        $requestBuilderMock = $this->getRequestBuilderMock($requestBuilderType, $requestStub, $providerRequestStub);

        $clientMock = $this->getHttpClientMock();
        $clientMock->expects($this->once())
            ->method('post')
            ->with($configuration->getApiUrl(), [
                'verify' => true,
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2
                ],
                'form_params' => $providerRequestStub,
            ])
            ->willReturn($httpClientResponseStub);

        $responseParserMock = $this->getResponseParserMock($responseParserType);
        $responseParserMock->expects($this->once())
            ->method('parse')
            ->with($httpClientResponseStub)
            ->willReturn($responseStub);

        $transaction = new $transactionType($configuration, $clientMock, $requestBuilderMock, $responseParserMock);
        $response = $transaction->perform($requestStub);

        $this->assertInstanceOf($responseType, $response);
    }

    /**
     * @param string $requestType
     * @param string $requestBuilderType
     * @param string $responseParserType
     * @param Throwable $error
     * @param string $transactionType
     * @dataProvider responseParserErrorProvider
     */
    public function test_perform_with_parser_errors(
        string $requestType,
        string $requestBuilderType,
        string $responseParserType,
        string $parserErrorType,
        string $transactionType
    ) {
        $this->expectException($parserErrorType);

        $configuration = $this->getConfiguration();

        $requestStub = $this->createStub($requestType);
        $providerRequestStub = ['field' => 'expected-value'];
        $clientErrorStub = $this->createStub($parserErrorType);
        $httpClientResponseStub = $this->createStub(ResponseInterface::class);

        $requestBuilderMock = $this->getRequestBuilderMock($requestBuilderType, $requestStub, $providerRequestStub);

        $clientMock = $this->getHttpClientMock();
        $clientMock->expects($this->once())
            ->method('post')
            ->with($configuration->getApiUrl(), [
                'verify' => true,
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2
                ],
                'form_params' => $providerRequestStub,
            ])
            ->willReturn($httpClientResponseStub);

        $responseParserMock = $this->getResponseParserMock($responseParserType);
        $responseParserMock->expects($this->once())
            ->method('parse')
            ->with($httpClientResponseStub)
            ->willThrowException($clientErrorStub);

        $transaction = new $transactionType($configuration, $clientMock, $requestBuilderMock, $responseParserMock);
        $transaction->perform($requestStub);
    }

    /**
     * @param string $requestType
     * @param string $requestBuilder
     * @param string $responseParserType
     * @param string $httpClientErrorType
     * @param string $transactionType
     * @dataProvider httpClientErrorProvider
     */
    public function test_http_client_errors(
        string $requestType,
        string $requestBuilder,
        string $responseParserType,
        string $httpClientErrorType,
        string $transactionType
    ) {
        $this->expectException($httpClientErrorType);

        $configuration = $this->getConfiguration();
        $requestStub = $this->createStub($requestType);
        $providerRequestStub = ['field' => 'expected-value'];
        $clientErrorStub = $this->createStub($httpClientErrorType);

        $requestBuilderMock = $this->getRequestBuilderMock($requestBuilder, $requestStub, $providerRequestStub);
        $responseParserMock = $this->getResponseParserMock($responseParserType);

        $clientMock = $this->getHttpClientMock();
        $clientMock->expects($this->once())
            ->method('post')
            ->with($configuration->getApiUrl(), [
                'verify' => true,
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2
                ],
                'form_params' => $providerRequestStub,
            ])
            ->willThrowException($clientErrorStub);

        $transaction = new $transactionType($configuration, $clientMock, $requestBuilderMock, $responseParserMock);
        $transaction->perform($requestStub);
    }


    /**
     * @return NestpayConfiguration
     */
    public function getConfiguration(): NestpayConfiguration
    {
        $configuration = new NestpayConfiguration();
        $configuration->setApiUrl('http://example.com');
        $configuration->setClientId('000001');
        $configuration->setUsername('NESTPAYUSER');
        $configuration->setPassword('NESTPAYPASS');
        return $configuration;
    }

    /**
     * @return MockObject
     */
    public function getHttpClientMock(): MockObject
    {
        return $this->getMockBuilder(Client::class)
            ->addMethods(['post'])
            ->getMock();
    }

    /**
     * @param string $requestBuilderType
     * @param $requestStub
     * @param array $providerRequestStub
     * @return MockObject
     */
    public function getRequestBuilderMock(string $requestBuilderType, $requestStub, array $providerRequestStub): MockObject
    {
        $requestBuilderMock = $this->getMockBuilder($requestBuilderType)
            ->disableOriginalConstructor()
            ->getMock();

        $requestBuilderMock->expects($this->once())
            ->method('build')
            ->with($requestStub)
            ->willReturn($providerRequestStub);
        return $requestBuilderMock;
    }

    /**
     * @param string $responseParserType
     * @return MockObject
     */
    public function getResponseParserMock(string $responseParserType): MockObject
    {
        return $this->getMockBuilder($responseParserType)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
