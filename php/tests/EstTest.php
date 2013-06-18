<?php
use \Payment\Adapter\Est;
use \Payment\Request;

class EstTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $config = new Zend_Config_Ini('config/payment.ini', APPLICATION_ENV);
        $this->_config = $config->isbank;
    }
    
    /**
    * creates a order request for test.
    * @param string $orderId 
    * @param float $amount
    * @return \Payment\Request
    */
    private function _createNewOrder($orderId = null, $amount = 100)
    {
        $request = new Request();
        if($orderId == null) {
            $request->setOrderId(time());
        }
        $request->setAmount($amount);
        $request->setCurrency('TRL');
        $request->setCardNumber('5406675406675403');
        $request->setSecurityCode('000');
        $request->setExpireMonth(12);
        $request->setExpireYear(2013);
        return $request;
    }
    
    /**
    * makes sale transaction.
    * 
    * @param \Payment\Request $request
    * @return \Payment\Response\PaymentResponse
    */
    private function _makeSale(Request $request)
    {
        $response = $this->_adapter->sale($request);
        $this->assertTrue($response->isSuccess());
        return $response;
    }
    
    /**
    * cancels transaction.
    * 
    * @param \Payment\Request $request
    * @return \Payment\Response\PaymentResponse
    */
    private function _makeCancel(Request $request)
    {
        $response = $this->_adapter->cancel($request);
        $this->assertTrue($response->isSuccess());
        return $response;
    }
    
    /**
    * makes refund request.
    * 
    * @param \Payment\Request $request
    * @param boolean $assertion
    * @return \Payment\Response\PaymentResponse
    */
    private function _makeRefund(Request $request, $assertion=true)
    {
        $response = $this->_adapter->refund($request);
        if($assertion) {
            $this->assertTrue($response->isSuccess());
        }
        return $response;
    }
    
    /**
    * this tet case performs the following test steps:
    * makes sale transaction.
    * make cancel transaction and canceling previous sale transaction.
    */
    public function testCase1()
    {
        $this->_adapter = new Est($this->_config);
        $request = $this->_createNewOrder();
        $this->_makeSale($request);
        $this->_makeCancel($request);
    }

    /**
    * this tet case performs the following test steps:
    * makes sale transaction.
    * makes full refund.
    */
    public function testCase2()
    {
        $this->_adapter = new Est($this->_config);
        $request = $this->_createNewOrder();
        $saleResponse = $this->_makeSale($request);
        $refundResponse = $this->_makeRefund($request);
    }
    
    /**
    * this tet case performs the following test steps:
    * makes sale transaction.
    * makes partial refund as TL2
    * makes partial refund as TL5
    * makes refund that greater then refundable amount.
    */
    public function testCase3()
    {
        $this->_adapter = new Est($this->_config);
        $request = $this->_createNewOrder(null, 10);
        $this->_makeSale($request);
        $request->setAmount(2);
        $this->_makeRefund($request);
        $request->setAmount(5);
        $this->_makeRefund($request);
        $request->setAmount(5);
        $response = $this->_makeRefund($request, false);
        $this->assertFalse($response->isSuccess());
    }
}
