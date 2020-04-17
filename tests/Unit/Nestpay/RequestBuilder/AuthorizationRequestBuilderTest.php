<?php
namespace Paranoia\Unit\Nestpay\RequestBuilder;

use Paranoia\Configuration\NestpayConfiguration;
use Paranoia\Core\Formatter\DecimalFormatter;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Core\Request\AuthorizationRequest;
use Paranoia\Core\Currency;
use Paranoia\Nestpay\Formatter\ExpireDateFormatter;
use Paranoia\Nestpay\RequestBuilder\AuthorizationRequestBuilder;
use PHPUnit\Framework\TestCase;

class AuthorizationRequestBuilderTest extends TestCase
{
    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            [null, __DIR__ . '/../../../stub/nestpay/request/authorization_without_installment.xml'],
            [1, __DIR__ . '/../../../stub/nestpay/request/authorization_without_installment.xml'],
            [8, __DIR__ . '/../../../stub/nestpay/request/authorization_with_greater_than_one_installment.xml'],
        ];
    }

    /**
     * @param int $installment
     * @param string $expectedXmlFilename
     * @dataProvider dataProvider
     */
    public function test_build(?int $installment, string $expectedXmlFilename): void
    {
        $configuration = $this->getConfiguration();
        $requestBuilder = $this->getRequestBuilder($configuration);
        $request = $this->getRequest($installment);

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
     * @return AuthorizationRequestBuilder
     */
    public function getRequestBuilder(NestpayConfiguration $configuration): AuthorizationRequestBuilder
    {
        return new AuthorizationRequestBuilder(
            $configuration,
            new DecimalFormatter(),
            new IsoNumericCurrencyCodeFormatter(),
            new ExpireDateFormatter(),
            new SingleDigitInstallmentFormatter()
        );
    }

    /**
     * @param int|null $installment
     * @return AuthorizationRequest
     */
    public function getRequest(?int $installment=null): AuthorizationRequest
    {
        $request = new AuthorizationRequest();
        $request->setOrderId('0000000001');
        $request->setCardNumber('5105105105105100');
        $request->setCardExpireMonth(5);
        $request->setCardExpireYear(2025);
        $request->setCardCvv('000');
        $request->setAmount(100.5);
        $request->setCurrency(Currency::CODE_TRY);

        if ($installment != null) {
            $request->setInstallment($installment);
        }

        return $request;
    }
}
