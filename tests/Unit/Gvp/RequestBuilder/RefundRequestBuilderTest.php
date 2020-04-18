<?php

namespace Paranoia\Test\Unit\Gvp\RequestBuilder;

use Paranoia\Configuration\GvpConfiguration;
use Paranoia\Core\Currency;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Request\RefundRequest;
use Paranoia\Gvp\RequestBuilder\RefundRequestBuilder;
use PHPUnit\Framework\TestCase;

class RefundRequestBuilderTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            // TODO: I've been disabled full refund (without amount)
            // since I'm not sure if other providers work without amount

            [100.5, Currency::CODE_TRY, __DIR__ . '/../../../stub/gvp/request/refund_with_amount.xml'],
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
     * @return RefundRequestBuilder
     */
    public function getRequestBuilder(GvpConfiguration $configuration): RefundRequestBuilder
    {
        return new RefundRequestBuilder(
            $configuration,
            new MoneyFormatter(),
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
