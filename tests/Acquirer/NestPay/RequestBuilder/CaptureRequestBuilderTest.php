<?php
namespace Paranoia\Test\Acquirer\NestPay\RequestBuilder;

use Paranoia\Acquirer\NestPay\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\NestPay\NestPayConfiguration as NestPayConfiguration;
use Paranoia\Acquirer\NestPay\RequestBuilder\CaptureRequestBuilder;
use Paranoia\Core\Formatter\DecimalFormatter;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Core\Model\Request;
use PHPUnit\Framework\TestCase;

class CaptureRequestBuilderTest extends TestCase
{
    public function test()
    {
        $builder = $this->setupBuilder();
        $request = $this->setupRequest();
        $rawRequest = $builder->build($request);
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../../../samples/request/nestpay/postauthorization_request.xml',
            $rawRequest
        );
    }

    protected function setupConfiguration()
    {
        $configuration = new NestPayConfiguration();
        $configuration->setClientId('123456')
            ->setMode('TEST')
            ->setUsername('TEST')
            ->setPassword('TEST');
        return $configuration;
    }

    /**
     * @param bool $isTransaction
     * @return Request
     */
    protected function setupRequest($isTransaction=false)
    {
        $request = new Request();
        $request->setOrderId('123456');
        if ($isTransaction) {
            $request->setTransactionId('123456');
        }
        return $request;
    }

    protected function setupBuilder()
    {
        return new CaptureRequestBuilder(
            $this->setupConfiguration(),
            new IsoNumericCurrencyCodeFormatter(),
            new DecimalFormatter(),
            new SingleDigitInstallmentFormatter(),
            new ExpireDateFormatter()
        );
    }
}
