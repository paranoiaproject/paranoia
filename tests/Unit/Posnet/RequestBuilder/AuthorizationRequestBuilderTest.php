<?php
namespace Paranoia\Test\Unit\Posnet\RequestBuilder;

use Paranoia\Configuration\PosnetConfiguration;
use Paranoia\Core\Currency;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\MultiDigitInstallmentFormatter;
use Paranoia\Core\Request\AuthorizationRequest;
use Paranoia\Posnet\Formatter\CustomCurrencyCodeFormatter;
use Paranoia\Posnet\Formatter\ExpireDateFormatter;
use Paranoia\Posnet\Formatter\OrderIdFormatter;
use Paranoia\Posnet\RequestBuilder\AuthorizationRequestBuilder;
use PHPUnit\Framework\TestCase;

class AuthorizationRequestBuilderTest extends TestCase
{
    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            [null, __DIR__ . '/../../../stub/posnet/request/authorization_without_installment.xml'],
            [1, __DIR__ . '/../../../stub/posnet/request/authorization_without_installment.xml'],
            [8, __DIR__ . '/../../../stub/posnet/request/authorization_with_installment.xml'],
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
     * @return AuthorizationRequestBuilder
     */
    public function getRequestBuilder(PosnetConfiguration $configuration): AuthorizationRequestBuilder
    {
        return new AuthorizationRequestBuilder(
            $configuration,
            new MoneyFormatter(),
            new CustomCurrencyCodeFormatter(),
            new ExpireDateFormatter(),
            new MultiDigitInstallmentFormatter(),
            new OrderIdFormatter()
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
