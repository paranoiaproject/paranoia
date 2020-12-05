<?php
namespace Paranoia\Test\Acquirer\Posnet\RequestBuilder;

use Paranoia\Acquirer\Posnet\RequestBuilder\PostAuthorizationRequestBuilder;
use Paranoia\Core\Constant\Currency;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\MultiDigitInstallmentFormatter;
use Paranoia\Acquirer\Posnet\Formatter\CustomCurrencyCodeFormatter;
use Paranoia\Acquirer\Posnet\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\Posnet\Formatter\OrderIdFormatter;
use Paranoia\Core\Model\Request;
use PHPUnit\Framework\TestCase;
use Paranoia\Acquirer\Posnet\PosnetConfiguration as PosnetConfiguration;

class PostAuthorizationRequestBuilderTest extends TestCase
{
    public function test_sales_with_single_installment()
    {
        $builder = $this->setupBuilder();
        $request = $this->setupRequest();
        $rawRequest = $builder->build($request);
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../../../samples/request/posnet/post_authorization_request_eur.xml',
            $rawRequest
        );
    }

    public function test_sales_with_multi_installment()
    {
        $builder = $this->setupBuilder();
        $request = $this->setupRequest(true);
        $rawRequest = $builder->build($request);
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../../../samples/request/posnet/post_authorization_request_eur_with_installment.xml',
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
        $request->setTransactionId('12345678901')
            ->setAmount(25.4)
            ->setCurrency(Currency::CODE_EUR);
        if($setInstallment) {
            $request->setInstallment(3);
        }
        return $request;
    }

    protected function setupBuilder()
    {
        return new PostAuthorizationRequestBuilder(
            $this->setupConfiguration(),
            new CustomCurrencyCodeFormatter(),
            new MoneyFormatter(),
            new MultiDigitInstallmentFormatter(),
            new ExpireDateFormatter(),
            new OrderIdFormatter()
        );
    }
}