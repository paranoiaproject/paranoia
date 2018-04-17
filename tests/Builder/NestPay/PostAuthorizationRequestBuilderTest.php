<?php
namespace Paranoia\Test\Builder\NestPay;

use Paranoia\Builder\NestPay\PostAuthorizationRequestBuilder;
use Paranoia\Configuration\NestPay as NestPayConfiguration;
use Paranoia\Formatter\DecimalFormatter;
use Paranoia\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Formatter\NestPay\ExpireDateFormatter;
use Paranoia\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Request;
use PHPUnit\Framework\TestCase;

class PostAuthorizationRequestBuilderTest extends TestCase
{
    public function test()
    {
        $builder = $this->setupBuilder();
        $request = $this->setupRequest();
        $rawRequest = $builder->build($request);
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../../samples/request/nestpay/postauthorization_request.xml',
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
        return new PostAuthorizationRequestBuilder(
            $this->setupConfiguration(),
            new IsoNumericCurrencyCodeFormatter(),
            new DecimalFormatter(),
            new SingleDigitInstallmentFormatter(),
            new ExpireDateFormatter()
        );
    }
}
