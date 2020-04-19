<?php
namespace Paranoia\Test\Unit\Posnet\RequestBuilder;

use Paranoia\Configuration\PosnetConfiguration;
use Paranoia\Core\Currency;
use Paranoia\Core\Formatter\DecimalFormatter;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Request\RefundRequest;
use Paranoia\Posnet\Formatter\CustomCurrencyCodeFormatter;
use Paranoia\Posnet\RequestBuilder\RefundRequestBuilder;
use PHPUnit\Framework\TestCase;

class RefundRequestBuilderTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            // TODO: I've been disabled full refund (without amount)
            // since I'm not sure if other providers work without amount

//            [null, null, __DIR__ . '/../../../stub/posnet/request/refund_without_amount.xml'],
            [100.5, Currency::CODE_TRY, __DIR__ . '/../../../stub/posnet/request/refund_with_amount.xml'],
//            [null, Currency::CODE_TRY, __DIR__ . '/../../../stub/posnet/request/refund_without_amount.xml'],
//            [100.5, null, __DIR__ . '/../../../stub/posnet/request/refund_without_amount.xml'],
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
     * @return RefundRequestBuilder
     */
    public function getRequestBuilder(PosnetConfiguration $configuration): RefundRequestBuilder
    {
        return new RefundRequestBuilder(
            $configuration,
            new MoneyFormatter(),
            new CustomCurrencyCodeFormatter()
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
        $request->setTransactionRef('0000000001');
        $request->setAmount($amount);
        $request->setCurrency($currency);
        return $request;
    }
}
