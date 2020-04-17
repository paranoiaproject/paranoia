<?php
namespace Paranoia\Test\Builder\NestPay;

use Paranoia\Builder\NestPay\PreAuthorizationRequestBuilder;
use Paranoia\Configuration\NestpayConfiguration as NestPayConfiguration;
use Paranoia\Currency;
use Paranoia\Formatter\DecimalFormatter;
use Paranoia\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Formatter\NestPay\ExpireDateFormatter;
use Paranoia\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Request\Request;
use Paranoia\Request\Resource\Card;
use PHPUnit\Framework\TestCase;

class PreAuthorizationRequestBuilderTest extends TestCase
{
    public function test_pre_auth()
    {
        $builder = $this->setupBuilder();

        $request = $this->setupRequest();
        $rawRequest = $builder->build($request);
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../../samples/request/nestpay/preauthorization_request.xml',
            $rawRequest
        );
    }

    protected function setupConfiguration()
    {
        $configuration = new NestPayConfiguration();
        $configuration->setClientId('123456')
            ->setMode('TEST')
            ->setUsername('TEST')
            ->setPassword('TEST');
        return $configuration;
    }

    /**
     * @param bool $setInstallment
     * @return Request
     */
    protected function setupRequest()
    {
        $request = new Request();
        $request->setOrderId('123456')
            ->setAmount(25.4)
            ->setCurrency(Currency::CODE_EUR);

        $card = new Card();
        $card->setNumber('1501501501501500')
            ->setSecurityCode('000')
            ->setExpireMonth(1)
            ->setExpireYear(2020);
        $request->setResource($card);

        return $request;
    }

    protected function setupBuilder()
    {
        return new PreAuthorizationRequestBuilder(
            $this->setupConfiguration(),
            new IsoNumericCurrencyCodeFormatter(),
            new DecimalFormatter(),
            new SingleDigitInstallmentFormatter(),
            new ExpireDateFormatter()
        );
    }
}
