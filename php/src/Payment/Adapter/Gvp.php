<?php
namespace Payment\Adapter;

use \Array2XML;

use \Payment\Request;
use \Payment\Response\PaymentResponse;

use \Payment\Adapter\AdapterInterface;
use \Payment\Adapter\AdapterAbstract;

use \Payment\Exception\UnexpectedResponse;

use \Communication\Connector;

class Gvp extends AdapterAbstract implements AdapterInterface
{
    const CONNECTOR_TYPE =  Connector::CONNECTOR_TYPE_HTTP;

	/**
	 * @var array
	 */
	protected $_transactionMap = array(self::TRANSACTION_TYPE_PREAUTHORIZATION  => 'preauth',
									   self::TRANSACTION_TYPE_POSTAUTHORIZATION => 'postauth',
									   self::TRANSACTION_TYPE_SALE 			  	=> 'sales',
									   self::TRANSACTION_TYPE_CANCEL 			=> 'void',
									   self::TRANSACTION_TYPE_REFUND 			=> 'refund');
    /**
	 * builds request base with common arguments.
	 * @param Request $request
	 * @return array
	 */
    private function _buildBaseRequest(Request $request)
    {
        $transactionType = $request->getTransactionType();
        $username    = ( in_array($transactionType, array('sale', 'inquiry')) ) ?
                            $this->_auth_username : $this->_refund_username;
        $hash        = $this->_getTransactionHash($request);
        $terminal    = $this->_buildTerminal($request, $username);
        $customer    = $this->_buildCustomer($request);
        $order       = $this->_buildOrder($request);
        $transaction = $this->_buildTransaction($request);

        return array('Version' 	   => '0.01',
					 'Mode' 	   => $config->mode,
					 'Terminal'    => $terminal,
                     'Order'       => $order,
                     'Customer'    => $customer,
                     'Transaction' => $transaction);
    }

    /**
     * builds terminal section of request.
     *
     * @param \Payment\Request $request
     * @param string $username
     * @param string $password
     * @return array
    */
    private function _buildTerminal(Request $request, $username, $hash)
    {
        $config = $this->_config;

        return array('ProvUserID' => $username,
                     'HashData'   => $hash,
                     'UserID'     => $username,
                     'ID'         => $config->terminal_id,
                     'MerchantID' => $config->merchant_id);
    }

    /**
     * builds customer section of request.
     *
     * @param \Payment\Request $request
     * @return array
    */
    private function _buildCustomer(Request $request)
    {
        /**
        * we don't want to share customer information
        * to bank.
        */
        return array('IPAddress'    => '127.0.0.1',
                     'EmailAddress' => 'dummy@dummy.net');
    }

    /**
     * builds card section of request.
     *
     * @param \Payment\Request $request
     * @return array
    */
    private function _buildCard(Request $request)
    {
        $expireMonth = $this->_formatExpireDate($request->getExpireMonth(),
                                                $request->getExpireYear());
        return array('Number'     => $request->getCardNumber(),
                     'ExpireDate' => $expireMonth,
                     'CVV2'       => $request->getSecurityCode());
    }

    /**
     * builds order section of request.
     *
     * @param \Payment\Request $request
     * @return array
    */
    private function _buildOrder(Request $request)
    {
        return array('OrderID'     => $request->getOrderId(),
                     'GroupID'     => null,
                     'Description' => null);
    }

    /**
     * builds terminal section of request.
     *
     * @param \Payment\Request $request
     * @param integer $cardHolderPresentCode
     * @param string $originalRetrefNum
     * @return array
    */

    private function _buildTransaction(Request $request, $cardHolderPresentCode = 0,
									   $originalRetrefNum = null)
    {
        $installment 	= ($request->getInstallment()) ?
                            	$this->_formatInstallment($request->getInstallment()) : null;
        $amount      	= ($request->getAmount()) ?
                            	$this->_formatAmount($request->getAmount()) : null;
        $currency    	=  ($request->getCurrency()) ?
                            	$this->_formatCurrency($request->getCurrency()) : null;
        $transactionType = $this->_getProviderTransactionType($request->getTransactionType);

        return array('Type'                  => $transactionType,
                     'InstallmentCnt'        => $installment,
                     'Amount'                => $amount,
                     'CurrencyCode'          => $currency,
                     'CardholderPresentCode' => $cardHolderPresentCode,
                     'MotoInd'               => 'N',
                     'OriginalRetrefNum'     => $originalRetrefNum);
    }

    /**
     * returns security hash for using in transaction hash.
     *
     * @param string $transactionType
     * @return string
     */
    private function _getSecurityHash($transactionType)
    {
        $config     = $this->_config;
        $password   = (in_array($transactionType, array('sale', 'inquiry'))) ?
                        $config->auth_password : $config->refund_password;a
        $terminalId = sprintf('%s%s', str_repeat('0', strlen($config->terminal_id)),
                                      $config->terminal_id);
        return strtoupper( SHA1( sprintf('%s%s', $password, $terminalId) ) );
    }

    /**
     * returns transaction hash for using in transaction request.
     *
     * @param \Payment\Request $request
     * @return string
     */
    private function _getTransactionHash(Request $request)
    {
        $config     	 = $this->_config;
        $orderId    	 = $request->getOrderId();
        $terminalId 	 = $config->getTerminalId();
		$transactionType = $request->getTransactionType();
        $cardNumber 	 = ( !in_array($transactionType, array('refund', 'cancel', 'inquiry')) ) ?
                            	$request->getCardNumber() : '';
        $amount     	 = ( !in_array($transactionType, array('cancel', 'inquiry')) ) ?
                            	$this->_formatAmount($request->getAmount()) : '1';
        $securityData 	 = $this->_getSecurityHash();
        return strtoupper( sha1( sprintf('%s%s%s%s%s',
                                        $orderId,
                                        $terminalId,
                                        $cardNumber,
                                        $amount,
                                        $securityData) ) );
    }

    /**
     * @see \Payment\Adapter\AdapterAbstract::_buildRequest()
     */
    protected function _buildRequest(Request $request, $requestBuilder)
    {
        $rawRequest = call_user_func(array($this, $requestBuilder), $request);
        $xml 		= Array2XML::createXML('GVPSRequest', $rawRequest);
        $data 		= array('data' => $xml->saveXml());

		$request->setRawData($xml);

        return http_build_query($data);
    }

    /**
     * @see \Payment\Adapter\AdapterAbstract::_buildPreauthorizationRequest()
     */
    protected function _buildPreauthorizationRequest(Request $request)
    {
		$requestData = array('Card' => $this->_buildCard($request));
		return array_merge($requestData, $this->_buildBaseRequest($request));
    }

    /**
     * @see \Payment\Adapter\AdapterAbstract::_buildPostAuthorizationRequest()
     */
    protected function _buildPostAuthorizationRequest(Request $request)
    {
		$requestData = $this->_buildBaseRequest($request);
		$requestData['Transaction']['Amount'] 		= null;
		$requestData['Transaction']['CurrencyCode'] = null;
		return $requestData;
    }

    /**
     * @see \Payment\Adapter\AdapterAbstract::_buildSaleRequest()
     */
    protected function _buildSaleRequest(Request $request)
    {
 		return $this->_buildBaseRequest($request);
    }

    /**
     * @see \Payment\Adapter\AdapterAbstract::_buildRefundRequest()
     */
    protected function _buildRefundRequest(Request $request)
    {
		return $this->_buildBaseRequest($request);
    }

    /**
     * @see \Payment\Adapter\AdapterAbstract::_buildCancelRequest()
     */
    protected function _buildCancelRequest(Request $request)
    {
		$requestData = $this->_buildBaseRequest($request);
		$transactionId = ($request->getTransactionId()) ?
							$request->getTransactionId() : null;
		$transaction =  $this->_buildTransaction($request, 0, $transactionId);
		$requestData['Transaction'] = $transaction;
		return $requestData;
    }

    /**
     * @see \Payment\Adapter\AdapterAbstract::_parseResponse()
     */
    protected function _parseResponse($rawResponse)
    {
		$response = new PaymentResponse();
        try {
            $xml = new \SimpleXmlElement($rawResponse);
        } catch(\Exception $e) {
            throw new UnexpectedResponse('Provider is returned unexpected ' .
                                         'response. Response data:' .
                                         $rawResponse);
        }
        $response->setIsSuccess( (string) $xml->Transaction->Response->Code == '00' );
        $response->setResponseCode( (string) $xml->Transaction->ReasonCode  );
        if( ! $response->isSuccess() ) {
            $errorMessages = array();

            if( property_exists($xml->Transaction->Response, 'ErrorMsg') ) {
                $errorMessages[] = sprintf('Error Message: %s',
										   (string) $xml->Transaction->Response->ErrorMsg);
            }

            if(property_exists($xml->Transaction->Response, 'SysErrMsg')) {
            	$errorMessages[] = sprintf('System Error Message: %s',
						                    (string) $xml->Transaction->Response->SysErrMsg);
			}

            $errorMessage = implode(' ', $errorMessages);
            $response->setResponseMessage($errorMessage);
        } else {
            $response->setResponseMessage('Success');
            $response->setOrderId( (string) $xml->Order->OrderID );
            $response->setTransactionId( (string) $xml->Transaction->RetrefNum );
        }
        $response->setRawData($rawResponse);
        return $response;
    }
}
