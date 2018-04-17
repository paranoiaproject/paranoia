<?php
namespace Paranoia\Test\Builder\NestPay;

use Paranoia\Builder\NestPay\RefundRequestBuilder;
use Paranoia\Configuration\NestPay as NestPayConfiguration;
use Paranoia\Currency;
use Paranoia\Formatter\DecimalFormatter;
use Paranoia\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Formatter\NestPay\ExpireDateFormatter;
use Paranoia\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Request;
use PHPUnit\Framework\TestCase;
#TODO: Ensure whether the provider allow full refund without amount and currency fields
class RefundRequestBuilderTest extends TestCase
{
    public function test_refund_full()
    {
        $builder = $this->setupBuilder();
        $request = $this->setupRequest();
        $rawRequest = $builder->build($request);
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../../samples/request/nestpay/refund_request_full.xml',
            $rawRequest
        );
    }

    public function test_partial_refund()
    {
        $builder = $this->setupBuilder();
        $request = $this->setupRequest(true);
        $rawRequest = $builder->build($request);
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../../samples/request/nestpay/refund_request_partial.xml',
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
     * @param bool $isPartial
     * @return Request
     */
    protected function setupRequest($isPartial=false)
    {
        $request = new Request();
        $request->setOrderId('123456');
        if($isPartial) {
            $request->setAmount(25.4)
                ->setCurrency(Currency::CODE_EUR);
        }
        return $request;
    }

    protected function setupBuilder()
    {
        return new RefundRequestBuilder(
            $this->setupConfiguration(),
            new IsoNumericCurrencyCodeFormatter(),
            new DecimalFormatter(),
            new SingleDigitInstallmentFormatter(),
            new ExpireDateFormatter()
        );
    }
}