<?php
namespace Paranoia\Test\Acquirer\NestPay\RequestBuilder;

use Paranoia\Acquirer\NestPay\RequestBuilder\CancelRequestBuilder;
use Paranoia\Acquirer\NestPay\NestPayConfiguration as NestPayConfiguration;
use Paranoia\Core\Formatter\DecimalFormatter;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Acquirer\NestPay\Formatter\ExpireDateFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Core\Model\Request;
use PHPUnit\Framework\TestCase;

class CancelRequestBuilderTest extends TestCase
{
    public function test_cancel_order()
    {
        $builder = $this->setupBuilder();
        $request = $this->setupRequest();
        $rawRequest = $builder->build($request);
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../../../samples/request/nestpay/cancel_request_with_order.xml',
            $rawRequest
        );
    }

    public function test_cancel_transaction()
    {
        $builder = $this->setupBuilder();
        $request = $this->setupRequest(true);
        $rawRequest = $builder->build($request);
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../../../samples/request/nestpay/cancel_request_with_transaction.xml',
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
        return new CancelRequestBuilder(
            $this->setupConfiguration(),
            new IsoNumericCurrencyCodeFormatter(),
            new DecimalFormatter(),
            new SingleDigitInstallmentFormatter(),
            new ExpireDateFormatter()
        );
    }
}