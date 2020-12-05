<?php
namespace Paranoia\Test\Acquirer\Posnet\RequestBuilder;

use Paranoia\Acquirer\Posnet\Formatter\CustomCurrencyCodeFormatter;
use Paranoia\Acquirer\Posnet\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\Posnet\Formatter\OrderIdFormatter;
use Paranoia\Acquirer\Posnet\PosnetConfiguration as PosnetConfiguration;
use Paranoia\Acquirer\Posnet\RequestBuilder\AuthorizationRequestBuilder;
use Paranoia\Core\Constant\Currency;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\MultiDigitInstallmentFormatter;
use Paranoia\Core\Model\Request;
use Paranoia\Core\Model\Request\Resource\Card;
use PHPUnit\Framework\TestCase;

class AuthorizationRequestBuilderTest extends TestCase
{
    public function test_sales_with_single_installment()
    {
        $builder = $this->setupBuilder();
        $request = $this->setupRequest();
        $rawRequest = $builder->build($request);
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../../../samples/request/posnet/pre_authorization_request_eur.xml',
            $rawRequest
        );
    }

    public function test_sales_with_multi_installment()
    {
        $builder = $this->setupBuilder();
        $request = $this->setupRequest(true);
        $rawRequest = $builder->build($request);
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../../../samples/request/posnet/pre_authorization_request_eur_with_installment.xml',
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
            ->setCurrency(Currency::CODE_EUR);

        if($setInstallment) {
            $request->setInstallment(3);
        }

        $card = new Card();
        $card->setNumber('1501501501501500')
            ->setSecurityCode('000')
            ->setExpireMonth(1)
            ->setExpireYear(2020);
        $request->setCard($card);

        return $request;
    }

    protected function setupBuilder()
    {
        return new AuthorizationRequestBuilder(
            $this->setupConfiguration(),
            new CustomCurrencyCodeFormatter(),
            new MoneyFormatter(),
            new MultiDigitInstallmentFormatter(),
            new ExpireDateFormatter(),
            new OrderIdFormatter()
        );
    }
}