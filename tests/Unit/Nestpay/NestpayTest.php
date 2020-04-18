<?php
namespace Paranoa\Test\Unit\Nestpay;

use GuzzleHttp\ClientInterface;
use Paranoia\Configuration\NestpayConfiguration;
use Paranoia\Core\Formatter\DecimalFormatter;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Core\Request\AuthorizationRequest;
use Paranoia\Core\Response\AuthorizationResponse;
use Paranoia\Core\Transformer\XmlTransformer;
use Paranoia\Nestpay\Formatter\ExpireDateFormatter;
use Paranoia\Nestpay\Nestpay;
use PHPUnit\Framework\TestCase;

class NestpayTest extends TestCase
{
    public function test_authorize(): void
    {
        $configurationStub = $this->createStub(NestpayConfiguration::class);
        $httpClientMock = $this->getMockBuilder(ClientInterface::class)->getMock();
        $transformerStub = $this->createStub(XmlTransformer::class);
        $amountFormatterStub = $this->createStub(DecimalFormatter::class);
        $currencyFormatterStub = $this->createStub(IsoNumericCurrencyCodeFormatter::class);
        $expireDateFormatterStub = $this->createStub(ExpireDateFormatter::class);
        $installmentFormatterStub = $this->createStub(SingleDigitInstallmentFormatter::class);
        $requestStub = $this->createStub(AuthorizationRequest::class);


        $provider = new Nestpay(
            $configurationStub,
            $httpClientMock,
            $transformerStub,
            $amountFormatterStub,
            $currencyFormatterStub,
            $expireDateFormatterStub,
            $installmentFormatterStub
        );

        $response = $provider->authorization($requestStub);
        $this->assertInstanceOf(AuthorizationResponse::class, $response);
    }
}
