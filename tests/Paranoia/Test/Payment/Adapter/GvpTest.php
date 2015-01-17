<?php
namespace Paranoia\Tests\Payment\Adapter;

use \PHPUnit_Framework_TestCase;
use \Exception;
use Paranoia\Payment\Request;
use Paranoia\Configuration\Gvp as Configuration;
use Paranoia\Payment\Adapter\Gvp as Adapter;

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
            throw new Exception('Configuration file does not exist.');
        }
        $config       = file_get_contents($configFile);
        $this->config = json_decode($config);
        $this->bank   = 'garantibank';
    }

    /**
     * @param string $orderId
     * @param int    $amount
     *
     * @return Request
     */
    private function createNewOrder($orderId = null, $amount = 10)
    {
        $testData = $this->config->{$this->bank}->testcard;
        $request  = new Request();
        if ($orderId == null) {
            $request->setOrderId(sprintf('PRNY%s%s', time(), rand(1, 9999)));
        } else {
            $request->setOrderId($orderId);
        }
        $request->setCardNumber($testData->number)
            ->setSecurityCode($testData->cvv)
            ->setExpireMonth($testData->expire_month)
            ->setExpireYear($testData->expire_year)
            ->setAmount($amount)
            ->setCurrency('TRY');
        return $request;
    }

    private function initializeAdapter()
    {
        $configuration = $this->createConfiguration();
        $adapter       = new Adapter($configuration);
        return $adapter;
    }

    private function createConfiguration()
    {
        $bankData      = $this->config->{$this->bank};
        $configuration = new Configuration();
        $configuration->setApiUrl($bankData->api_url)
            ->setMerchantId($bankData->merchant_id)
            ->setTerminalId($bankData->terminal_id)
            ->setAuthorizationUsername($bankData->auth_username)
            ->setAuthorizationPassword($bankData->auth_password)
            ->setRefundUsername($bankData->refund_username)
            ->setRefundPassword($bankData->refund_password)
            ->setMode($bankData->mode);
        return $configuration;
    }

    public function testSale()
    {
        $instance     = $this->initializeAdapter();
        $orderRequest = $this->createNewOrder();
        $response     = $instance->sale($orderRequest);
        $this->assertTrue($response->isSuccess());
        if (!$response->isSuccess()) {
            fwrite(STDERR, $response->getRawData());
        }
        return $orderRequest;
    }

    /**
     * @depends testSale
     *
     * @param Request $saleRequest
     */
    public function testCancel(Request $saleRequest)
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
