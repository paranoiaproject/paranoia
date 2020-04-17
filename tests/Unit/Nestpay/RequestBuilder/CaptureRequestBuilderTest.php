<?php
namespace Paranoia\Test\Unit\Nestpay\RequestBuilder;

use Paranoia\Configuration\NestpayConfiguration;
use Paranoia\Core\Currency;
use Paranoia\Core\Formatter\DecimalFormatter;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Request\CaptureRequest;
use Paranoia\Nestpay\RequestBuilder\CaptureRequestBuilder;
use PHPUnit\Framework\TestCase;

class CaptureRequestBuilderTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            [null, null, __DIR__ . '/../../../stub/nestpay/request/capture_without_amount.xml'],
            [100.5, Currency::CODE_TRY, __DIR__ . '/../../../stub/nestpay/request/capture_with_amount.xml'],
            [null, Currency::CODE_TRY, __DIR__ . '/../../../stub/nestpay/request/capture_without_amount.xml'],
            [100.5, null, __DIR__ . '/../../../stub/nestpay/request/capture_without_amount.xml'],
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
     * @return CaptureRequestBuilder
     */
    public function getRequestBuilder(NestpayConfiguration $configuration): CaptureRequestBuilder
    {
        return new CaptureRequestBuilder(
            $configuration,
            new DecimalFormatter(),
            new IsoNumericCurrencyCodeFormatter()
        );
    }

    /**
     * @param float|null $amount
     * @param string|null $currency
     * @return CaptureRequest
     */
    public function getRequest(?float $amount, ?string $currency): CaptureRequest
    {
        $request = new CaptureRequest();
        $request->setOrderId('0000000001');
        $request->setAmount($amount);
        $request->setCurrency($currency);
        return $request;
    }
}
