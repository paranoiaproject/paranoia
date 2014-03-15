<?php
namespace Paranoia\Tests\Payment\Adapter;

use \PHPUnit_Framework_TestCase;
use \Exception;
use Paranoia\Payment\Factory;
use Paranoia\Payment\Request;
use Paranoia\EventManager\Listener\CommunicationListener;

class GvpTest extends PHPUnit_Framework_TestCase
{

    private $config;

    private $bank;

    public function setUp()
    {
        parent::setUp();
        parent::setUp();
        $configFile = dirname(__FILE__) . '/../../../../Resources/config/config.json';
        if (!file_exists($configFile)) {
            throw new Exception( 'Configuration file does not exist.' );
        }
        $config       = file_get_contents($configFile);
        $this->config = json_decode($config);
        $this->bank   = 'garantibank';
    }

    private function createNewOrder( $orderId = null, $amount = 10 )
    {
        $testData = $this->config->{$this->bank}->testcard;
        $request = new Request();
        if($orderId == null) {
            $request->setOrderId(sprintf('PRNY%s%s', time(), rand(1,9999)));
        } else {
            $request->setOrderId($orderId);
        }
        $request->setAmount($amount);
        $request->setCurrency('TRY');
        $request->setCardNumber($testData->number);
        $request->setSecurityCode($testData->cvv);
        $request->setExpireMonth($testData->expire_month);
        $request->setExpireYear($testData->expire_year);
        return $request;
    }

    private function initializeAdapter()
    {
        $instance = Factory::createInstance($this->config, $this->bank);
        // remove comment character from the following lines to
        // displaying transaction logs.
        $listener = new CommunicationListener();
        $instance->getConnector()->addListener('BeforeRequest', $listener);
        $instance->getConnector()->addListener('AfterRequest', $listener);
        return $instance;
    }

    public function testSale()
    {
        $instance     = $this->initializeAdapter();
        $orderRequest = $this->createNewOrder();
        $response     = $instance->sale($orderRequest);
        $this->assertTrue($response->isSuccess());
        return $orderRequest;
    }

    /**
     * @depends testSale
     */
    public function testCancel( Request $saleRequest )
    {
        $instance = $this->initializeAdapter();
        $request  = $this->createNewOrder($saleRequest->getOrderId());
        $response = $instance->cancel($request);
        $this->assertTrue($response->isSuccess());
    }

    public function testRefund()
    {
        $instance     = $this->initializeAdapter();
        $orderRequest = $this->createNewOrder();
        $response     = $instance->sale($orderRequest);
        $this->assertTrue($response->isSuccess());
        $refundRequest = $this->createNewOrder($orderRequest->getOrderId());
        $response      = $instance->refund($refundRequest);
        $this->assertTrue($response->isSuccess());
    }

    public function testPartialRefund()
    {
        $amount        = 10;
        $partialAmount = 5;
        $instance      = $this->initializeAdapter();
        $orderRequest  = $this->createNewOrder(null, $amount);
        $response      = $instance->sale($orderRequest);
        $this->assertTrue($response->isSuccess());
        $refundRequest = $this->createNewOrder($orderRequest->getOrderId(), $partialAmount);
        $response      = $instance->refund($refundRequest);
        $this->assertTrue($response->isSuccess());
        $refundRequest = $this->createNewOrder($orderRequest->getOrderId(), $partialAmount);
        $response      = $instance->refund($refundRequest);
        $this->assertTrue($response->isSuccess());
    }
}
