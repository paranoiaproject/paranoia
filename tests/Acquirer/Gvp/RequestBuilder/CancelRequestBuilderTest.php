<?php
namespace Paranoia\Test\Acquirer\Gvp\RequestBuilder;

use Paranoia\Acquirer\Gvp\RequestBuilder\CancelRequestBuilder;
use Paranoia\Acquirer\Gvp\GvpConfiguration as GvpConfiguration;
use Paranoia\Acquirer\Gvp\Formatter\ExpireDateFormatter;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Core\Model\Request;
use PHPUnit\Framework\TestCase;

class CancelRequestBuilderTest extends TestCase
{
    public function test_cancel_order()
    {
        $request = $this->setupRequest();
        $builder = $this->setupBuilder();
        $rawRequest = $builder->build($request);
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../../../samples/request/gvp/cancel_request_with_order.xml',
            $rawRequest
        );
    }

    public function test_cancel_transaction()
    {
        $request = $this->setupRequest(true);
        $builder = $this->setupBuilder();
        $rawRequest = $builder->build($request);
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../../../samples/request/gvp/cancel_request_with_transaction.xml',
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
     * @param bool $isTransaction
     * @return Request
     */
    protected function setupRequest($isTransaction=false)
    {
        $request = new Request();
        $request->setOrderId('123456');
        if ($isTransaction) {
            $request->setTransactionId('123456');
        }
        return $request;
    }

    protected function setupBuilder()
    {
        return new CancelRequestBuilder(
            $this->setupConfiguration(),
            new IsoNumericCurrencyCodeFormatter(),
            new MoneyFormatter(),
            new SingleDigitInstallmentFormatter(),
            new ExpireDateFormatter()
        );
    }
}