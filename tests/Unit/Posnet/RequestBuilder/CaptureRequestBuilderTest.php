<?php
namespace Paranoia\Test\Unit\Posnet\RequestBuilder;

use Paranoia\Configuration\PosnetConfiguration;
use Paranoia\Core\Currency;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Request\CaptureRequest;
use Paranoia\Posnet\Formatter\CustomCurrencyCodeFormatter;
use Paranoia\Posnet\RequestBuilder\CaptureRequestBuilder;
use PHPUnit\Framework\TestCase;

class CaptureRequestBuilderTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            // TODO: I've ignored partial capture for know since Garanti does not support. I'm not sure. Will check it
            [100.5, Currency::CODE_TRY, __DIR__ . '/../../../stub/posnet/request/capture_with_amount.xml'],
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
        $this->assertEquals('xmldata', $formParamKey);
        $this->assertXmlStringEqualsXmlFile(
            $expectedXmlFilename,
            $formParamValue
        );
    }

    /**
     * @return PosnetConfiguration
     */
    public function getConfiguration(): PosnetConfiguration
    {
        $configuration = new PosnetConfiguration();
        $configuration->setApiUrl('http://example.com');
        $configuration->setTerminalId('1000000002');
        $configuration->setMerchantId('3000040005');
        $configuration->setUsername('POSNETUSER');
        $configuration->setPassword('POSNETPASS');
        return $configuration;
    }

    /**
     * @param PosnetConfiguration $configuration
     * @return CaptureRequestBuilder
     */
    public function getRequestBuilder(PosnetConfiguration $configuration): CaptureRequestBuilder
    {
        return new CaptureRequestBuilder(
            $configuration,
            new MoneyFormatter(),
            new CustomCurrencyCodeFormatter()
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
        $request->setTransactionRef('0000000001');
        $request->setAmount($amount);
        $request->setCurrency($currency);
        return $request;
    }
}
