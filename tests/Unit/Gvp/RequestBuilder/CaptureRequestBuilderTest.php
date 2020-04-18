<?php

namespace Paranoia\Test\Unit\Gvp\RequestBuilder;

use Paranoia\Configuration\GvpConfiguration;
use Paranoia\Core\Currency;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Request\CaptureRequest;
use Paranoia\Gvp\RequestBuilder\CaptureRequestBuilder;
use PHPUnit\Framework\TestCase;

class CaptureRequestBuilderTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            // TODO: I've ignored partial capture for know since Garanti does not support. I'm not sure. Will check it
            [100.5, Currency::CODE_TRY, __DIR__ . '/../../../stub/gvp/request/capture_with_amount.xml'],
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
        $this->assertEquals('data', $formParamKey);
        $this->assertXmlStringEqualsXmlFile(
            $expectedXmlFilename,
            $formParamValue
        );
    }

    /**
     * @return GvpConfiguration
     */
    public function getConfiguration(): GvpConfiguration
    {
        $configuration = new GvpConfiguration();
        $configuration->setAuthorizationUsername('PROVAUT');
        $configuration->setAuthorizationPassword('PROVAUT');
        $configuration->setRefundUsername('PROVRFN');
        $configuration->setRefundPassword('PROVRFN');
        $configuration->setMerchantId('123456');
        $configuration->setTerminalId('654321');
        $configuration->setMode('TEST');
        return $configuration;
    }

    /**
     * @param GvpConfiguration $configuration
     * @return CaptureRequestBuilder
     */
    public function getRequestBuilder(GvpConfiguration $configuration): CaptureRequestBuilder
    {
        return new CaptureRequestBuilder(
            $configuration,
            new MoneyFormatter(),
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
