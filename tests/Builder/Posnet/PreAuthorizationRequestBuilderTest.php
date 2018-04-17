<?php
namespace Paranoia\Test\Builder\Posnet;

use Paranoia\Builder\Posnet\PreAuthorizationRequestBuilder;
use Paranoia\Currency;
use Paranoia\Formatter\MoneyFormatter;
use Paranoia\Formatter\MultiDigitInstallmentFormatter;
use Paranoia\Formatter\Posnet\CustomCurrencyCodeFormatter;
use Paranoia\Formatter\Posnet\ExpireDateFormatter;
use Paranoia\Formatter\Posnet\OrderIdFormatter;
use Paranoia\Request;
use PHPUnit\Framework\TestCase;
use Paranoia\Configuration\Posnet as PosnetConfiguration;

class PreAuthorizationRequestBuilderTest extends TestCase
{
    public function test_sales_with_single_installment()
    {
        $builder = $this->setupBuilder();
        $request = $this->setupRequest();
        $rawRequest = $builder->build($request);
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../../samples/request/posnet/pre_authorization_request_eur.xml',
            $rawRequest
        );
    }

    public function test_sales_with_multi_installment()
    {
        $builder = $this->setupBuilder();
        $request = $this->setupRequest(true);
        $rawRequest = $builder->build($request);
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../../samples/request/posnet/pre_authorization_request_eur_with_installment.xml',
            $rawRequest
        );
    }

    protected function setupConfiguration()
    {
        $configuration = new PosnetConfiguration();
        $configuration->setMerchantId('213456')
            ->setTerminalId('654321')
            ->setUsername('TEST')
            ->setPassword('TEST');
        return $configuration;
    }

    /**
     * @param bool $setInstallment
     * @return Request
     */
    protected function setupRequest($setInstallment=false)
    {
        $request = new Request();
        $request->setOrderId('123456')
            ->setAmount(25.4)
            ->setCurrency(Currency::CODE_EUR)
            ->setCardNumber('1501501501501500')
            ->setSecurityCode('000')
            ->setExpireMonth(1)
            ->setExpireYear(2020);
        if($setInstallment) {
            $request->setInstallment(3);
        }
        return $request;
    }

    protected function setupBuilder()
    {
        return new PreAuthorizationRequestBuilder(
            $this->setupConfiguration(),
            new CustomCurrencyCodeFormatter(),
            new MoneyFormatter(),
            new MultiDigitInstallmentFormatter(),
            new ExpireDateFormatter(),
            new OrderIdFormatter()
        );
    }
}