<?php
namespace Paranoia\Test\Acquirer\Gvp\RequestBuilder;

use Paranoia\Acquirer\Gvp\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\Gvp\GvpConfiguration as GvpConfiguration;
use Paranoia\Acquirer\Gvp\RequestBuilder\RefundRequestBuilder;
use Paranoia\Core\Constant\Currency;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Core\Model\Request;
use PHPUnit\Framework\TestCase;

class RefundRequestBuilderTest extends TestCase
{
    public function test()
    {
        $builder = $this->setupBuilder();
        $request = $this->setupRequest();

        $rawRequest = $builder->build($request);

        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../../../samples/request/gvp/refund_request.xml',
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
            ->setCurrency(Currency::CODE_EUR);
        return $request;
    }

    protected function setupBuilder()
    {
        return new RefundRequestBuilder(
            $this->setupConfiguration(),
            new IsoNumericCurrencyCodeFormatter(),
            new MoneyFormatter(),
            new SingleDigitInstallmentFormatter(),
            new ExpireDateFormatter()
        );
    }
}