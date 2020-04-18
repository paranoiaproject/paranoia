<?php

namespace Paranoia\Test\Unit\Gvp\RequestBuilder;

use Paranoia\Configuration\GvpConfiguration;
use Paranoia\Core\Currency;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Core\Request\AuthorizationRequest;
use Paranoia\Gvp\Formatter\ExpireDateFormatter;
use Paranoia\Gvp\RequestBuilder\AuthorizationRequestBuilder;
use PHPUnit\Framework\TestCase;

class AuthorizationRequestBuilderTest extends TestCase
{
    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            [null, __DIR__ . '/../../../stub/gvp/request/authorization_without_installment.xml'],
            [1, __DIR__ . '/../../../stub/gvp/request/authorization_without_installment.xml'],
            [8, __DIR__ . '/../../../stub/gvp/request/authorization_with_installment.xml'],
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
     * @return AuthorizationRequestBuilder
     */
    public function getRequestBuilder(GvpConfiguration $configuration): AuthorizationRequestBuilder
    {
        return new AuthorizationRequestBuilder(
            $configuration,
            new MoneyFormatter(),
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
