<?php
namespace Paranoia\Test\Builder\Gvp;

use Paranoia\Builder\Gvp\PreAuthorizationRequestBuilder;
use Paranoia\Currency;
use Paranoia\Formatter\Gvp\ExpireDate;
use Paranoia\Formatter\Gvp\ExpireDateFormatter;
use Paranoia\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Formatter\MoneyFormatter;
use Paranoia\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Request;
use PHPUnit\Framework\TestCase;
use Paranoia\Configuration\Gvp as GvpConfiguration;

class PreAuthorizationRequestBuilderTest extends TestCase
{
    public function test_pre_auth()
    {
        $builder = $this->setupBuilder();

        $request = $this->setupRequest();
        $rawRequest = $builder->build($request);
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../../samples/request/gvp/preauthorization_request.xml',
            $rawRequest
        );
    }

    protected function setupConfiguration()
    {
        $configuration = new GvpConfiguration();
        $configuration->setTerminalId('123456')
            ->setMode('TEST')
            ->setAuthorizationUsername('PROVAUT')
            ->setAuthorizationPassword('PROVAUT')
            ->setRefundUsername('PROVRFN')
            ->setRefundPassword('PROVRFN');

        return $configuration;
    }

    /**
     * @return Request
     */
    protected function setupRequest()
    {
        $request = new Request();
        $request->setOrderId('123456')
            ->setAmount(25.4)
            ->setCurrency(Currency::CODE_EUR)
            ->setCardNumber('1501501501501500')
            ->setSecurityCode('000')
            ->setExpireMonth(1)
            ->setExpireYear(2020);
        return $request;
    }

    protected function setupBuilder()
    {
        return new PreAuthorizationRequestBuilder(
            $this->setupConfiguration(),
            new IsoNumericCurrencyCodeFormatter(),
            new MoneyFormatter(),
            new SingleDigitInstallmentFormatter(),
            new ExpireDateFormatter()
        );
    }
}