<?php
namespace Paranoia\Test\Builder\NestPay;

use Paranoia\Builder\NestPay\CancelRequestBuilder;
use Paranoia\Configuration\NestPay as NestPayConfig;
use Paranoia\Test\BaseTestCase;
use Paranoia\Transfer\Request\CancelRequest;

class CancelRequestBuilderTest extends BaseTestCase
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

        $this->adapter = new CancelRequestBuilder($config);
    }

    public function testBuildWithOrderId()
    {
        $request = new CancelRequest();

        $request->setOrderId('0000000001');

        $rawRequest = $this->adapter->build($request);

        $this->assertTrue(is_array($rawRequest));
        $this->assertArrayHasKey('DATA', $rawRequest);
        $this->assertTrue(is_string($rawRequest['DATA']));

        $this->assertXmlStringEqualsXmlFile($this->getMockDataFile('WithOrderId'), $rawRequest['DATA']);
    }

    public function testBadRequest()
    {
        //TODO: validators must implement. It must run before building request data.
    }

    public function testBuildWithTransactionId()
    {
        $request = new CancelRequest();

        $request->setOrderId('0000000001')
                ->setTransactionId('0000000001');

        $rawRequest = $this->adapter->build($request);

        $this->assertTrue(is_array($rawRequest));
        $this->assertArrayHasKey('DATA', $rawRequest);
        $this->assertTrue(is_string($rawRequest['DATA']));

        $this->assertXmlStringEqualsXmlFile($this->getMockDataFile('WithTransactionId'), $rawRequest['DATA']);
    }
}
