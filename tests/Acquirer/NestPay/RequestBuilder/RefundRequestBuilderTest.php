<?php
namespace Paranoia\Test\Acquirer\NestPay\RequestBuilder;

use Paranoia\Acquirer\NestPay\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\NestPay\NestPayConfiguration as NestPayConfiguration;
use Paranoia\Acquirer\NestPay\RequestBuilder\RefundRequestBuilder;
use Paranoia\Core\Constant\Currency;
use Paranoia\Core\Formatter\DecimalFormatter;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Core\Model\Request;
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
            __DIR__ . '/../../../samples/request/nestpay/refund_request_full.xml',
            $rawRequest
        );
    }

    public function test_partial_refund()
    {
        $builder = $this->setupBuilder();
        $request = $this->setupRequest(true);
        $rawRequest = $builder->build($request);
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../../../samples/request/nestpay/refund_request_partial.xml',
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