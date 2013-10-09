<?php
use \Payment\Factory;
use \Payment\Request;

use \EventManager\Listener\CommunicationListener;

class IntegrationTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function setUp()
    {
        $config = include(dirname(__FILE__) . '/config.php');
        $this->_config = json_decode(json_encode($config));
    }

    private function _initAdapter($bank)
    {
        $this->_adapter = Factory::createInstance($this->_config, $bank);
        $listener = new CommunicationListener();
        $this->_adapter->getConnector()->addListener('BeforeRequest', $listener);
        $this->_adapter->getConnector()->addListener('AfterRequest', $listener);
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

    public function getBankList()
    {
        return array(array('isbank'));
#return array(array('isbank'));
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

    private function _makePreauthorization(Request $request)
    {
        $response = $this->_adapter->preAuthorization($request);
        $this->assertTrue($response->isSuccess());
        return $response;
    }

    private function _makePostAuthorization(Request $request)
    {
        $response = $this->_adapter->postAuthorization($request);
        $this->assertTrue($response->isSuccess());
        return $response;
    }

    /**
    * this tet case performs the following test steps:
    * makes sale transaction.
    * make cancel transaction and canceling previous sale transaction.
    * @dataProvider getBankList
    */
    public function testCase1($bank)
    {
        $this->_initAdapter($bank);
        $request = $this->_createNewOrder();
        $this->_makeSale($request);
        $this->_makeCancel($request);
    }

    /**
    * this tet case performs the following test steps:
    * makes sale transaction.
    * makes full refund.
    * @dataProvider getBankList
    */
    public function testCase2($bank)
    {
        $this->_initAdapter($bank);
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
    * @dataProvider getBankList
    */
    public function testCase3($bank)
    {
        $this->_initAdapter($bank);
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

    /**
     * this test case performs the following test steps:
     * makes preauthorization transaction.
     * makes postauthorization transaction.
     * cancels postauthorization transaction.
     * cancels preauthorization transaction.
     * @dataProvider getBankList
     */
    public function testCase4($bank)
    {

        $request = $this->_createNewOrder();
        $this->_initAdapter($bank);
        $response1 = $this->_makePreauthorization($request);
        $response2 = $this->_makePostAuthorization($request);
        $request->setTransactionId($response2->getTransactionId());
        $this->_makeCancel($request);
        $request->setTransactionId($response1->getTransactionId());
        $this->_makeCancel($request);
    }
}
