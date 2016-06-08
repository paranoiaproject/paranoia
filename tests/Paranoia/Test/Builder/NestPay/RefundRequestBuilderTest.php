<?php
namespace Paranoia\Test\Builder\NestPay;

use Paranoia\Builder\NestPay\RefundRequestBuilder;
use Paranoia\Configuration\NestPay as NestPayConfig;
use Paranoia\Test\BaseTestCase;
use Paranoia\Transfer\Request\RefundRequest;
use SebastianBergmann\Money\Currency;

class RefundRequestBuilderTest extends BaseTestCase
{
    /**
     * @var \Paranoia\Builder\BuilderInterface
     */
    private $adapter;

    public function setUp()
    {
        parent::setUp();
        $config = new NestPayConfig();
        $config->setClientId(16110)
            ->setMode('T')
            ->setUsername('APIUSER')
            ->setPassword('123456');

        $this->adapter = new RefundRequestBuilder($config);
    }

    public function testBadRequest()
    {
        //TODO: validators must implement. It must run before building request data.
    }


    public function testBuild()
    {
        $request = new RefundRequest();

        $request->setOrderId('0000000001')
            ->setAmount('100')
            ->setCurrency(new Currency('TRY'));

        $rawRequest = $this->adapter->build($request);

        $this->assertTrue(is_array($rawRequest));
        $this->assertArrayHasKey('DATA', $rawRequest);
        $this->assertTrue(is_string($rawRequest['DATA']));

        $this->assertXmlStringEqualsXmlFile($this->getMockDataFile(), $rawRequest['DATA']);
    }
}
