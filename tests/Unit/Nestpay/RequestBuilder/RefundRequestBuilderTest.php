<?php

namespace Paranoia\Test\Unit\Nestpay\RequestBuilder;

use Paranoia\Configuration\NestpayConfiguration;
use Paranoia\Core\Currency;
use Paranoia\Core\Formatter\DecimalFormatter;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Request\RefundRequest;
use Paranoia\Nestpay\RequestBuilder\RefundRequestBuilder;
use PHPUnit\Framework\TestCase;

class RefundRequestBuilderTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            // TODO: I've been disabled full refund (without amount)
            // since I'm not sure if other providers work without amount

//            [null, null, __DIR__ . '/../../../stub/nestpay/request/refund_without_amount.xml'],
            [100.5, Currency::CODE_TRY, __DIR__ . '/../../../stub/nestpay/request/refund_with_amount.xml'],
//            [null, Currency::CODE_TRY, __DIR__ . '/../../../stub/nestpay/request/refund_without_amount.xml'],
//            [100.5, null, __DIR__ . '/../../../stub/nestpay/request/refund_without_amount.xml'],
        ];
    }

    /**
     * @param float|null $amount
     * @param string|null $currency
     * @param string $expectedXmlFilename
     * @dataProvider dataProvider
     */
    public function test_build(?float $amount, ?string $currency, string $expectedXmlFilename):void
    {
        $configuration = $this->getConfiguration();
        $requestBuilder = $this->getRequestBuilder($configuration);
        $request = $this->getRequest($amount, $currency);

        $providerRequest = $requestBuilder->build($request);

        $formParamKey = array_shift(array_keys($providerRequest));
        $formParamValue = array_shift(array_values($providerRequest));
        $this->assertEquals('DATA', $formParamKey);
        $this->assertXmlStringEqualsXmlFile(
            $expectedXmlFilename,
            $formParamValue
        );
    }

    /**
     * @return NestpayConfiguration
     */
    public function getConfiguration(): NestpayConfiguration
    {
        $configuration = new NestpayConfiguration();
        $configuration->setClientId('000001');
        $configuration->setUsername('NESTPAYUSER');
        $configuration->setPassword('NESTPAYPASS');
        return $configuration;
    }

    /**
     * @param NestpayConfiguration $configuration
     * @return RefundRequestBuilder
     */
    public function getRequestBuilder(NestpayConfiguration $configuration): RefundRequestBuilder
    {
        return new RefundRequestBuilder(
            $configuration,
            new DecimalFormatter(),
            new IsoNumericCurrencyCodeFormatter()
        );
    }

    /**
     * @param float|null $amount
     * @param string|null $currency
     * @return RefundRequest
     */
    public function getRequest(?float $amount, ?string $currency): RefundRequest
    {
        $request = new RefundRequest();
        $request->setOrderId('0000000001');
        $request->setAmount($amount);
        $request->setCurrency($currency);
        return $request;
    }
}
