<?php
require('libs/bootstrap.php');

use \Payment\Factory;
use \Payment\Request;


class Test
{
    public function setUp()
    {
        $config = new Zend_Config_Ini('config/payment.ini', APPLICATION_ENV);
        $this->_config  = $config;
    }

    public function Test()
    {
        $this->setUp();
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
        $request->setCurrency('TRY');
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
        return $response;
    }

    private function _makePreauthorization(Request $request)
    {
        $response = $this->_adapter->preAuthorization($request);
        return $response;
    }

    private function _makePostAuthorization(Request $request)
    {
        $response = $this->_adapter->postAuthorization($request);
        return $response;
    }

    public function testCase1($bank)
    {
        $this->_adapter = Factory::createInstance($this->_config, $bank);
        $request = $this->_createNewOrder();
        $this->_makeSale($request);
        $this->_makeCancel($request);
    }
}

$test = new Test();
$test->testCase1('garanti');
