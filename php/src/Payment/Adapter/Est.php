<?php
namespace Payment\Adapter;

use \Array2XML;

use \Payment\Request;
use Payment\Response\PaymentResponse;
use \Payment\Adapter\AdapterInterface;
use \Payment\Adapter\Container\Http;
use \Payment\Exception\UnexpectedResponse;

class Est extends Http implements AdapterInterface
{
    private function _buildBaseRequest()
    {
        $config = $this->_config;
        return array('Name'     => $config->username,
                     'Password' => $config->password,
                     'ClientId' => $config->client_id,
                     'Mode'     => $config->mode);
    }
    
    /**
     * @see \Payment\Adapter\AdapterAbstract::_buildRequest()
     */
    protected function _buildRequest(Request $request, $requestBuilder)
    {   
        $rawRequest = call_user_func(array($this, $requestBuilder), $request);
        $xml = Array2XML::createXML('CC5Request', 
                                array_merge($rawRequest, 
                                            $this->_buildBaseRequest()));

        $data = array('DATA' => $xml->saveXml());
        $request->setRawData($xml);
        return http_build_query($data);
    }
    
    /**
     * @see \Payment\Adapter\AdapterAbstract::_buildPreauthorizationRequest()
     */
    protected function _buildPreauthorizationRequest(Request $request)
    {
        $amount      = $this->_formatAmount($request->getAmount());
        $installment = $this->_formatInstallment($request->getInstallment());
        $currency    = $this->_formatCurrency($request->getCurrency());
        $expireMonth = $this->_formatExpireDate($request->getExpireMonth(), 
                                                $request->getExpireYear());

        $requestData = array('Type'     => 'PreAuth',
                             'Total'    => $amount,
                             'Currency' => $currency,
                             'Taksit'   => $installment,
                             'Number'   => $request->getCardNumber(),
                             'Cvv2Val'  => $request->getSecurityCode(),
                             'Expires'  => $expireMonth,
                             'OrderId'  => $request->getOrderId(), );

        return $requestData;
    }

    /**
     * @see \Payment\Adapter\AdapterAbstract::_buildPostAuthorizationRequest()
     */
    protected function _buildPostAuthorizationRequest(Request $request)
    {
        $requestData = array('Type'     => 'PostAuth',
                             'OrderId'  => $request->getOrderId(), );

        return $requestData;
    }

    /**
     * @see \Payment\Adapter\AdapterAbstract::_buildSaleRequest()
     */
    protected function _buildSaleRequest(Request $request)
    {
        $amount      = $this->_formatAmount($request->getAmount());
        $installment = $this->_formatInstallment($request->getInstallment());
        $currency    = $this->_formatCurrency($request->getCurrency());
        $expireMonth = $this->_formatExpireDate($request->getExpireMonth(), 
                                                $request->getExpireYear());

        $requestData = array('Type'     => 'Auth',
                             'Total'    => $amount,
                             'Currency' => $currency,
                             'Taksit'   => $installment,
                             'Number'   => $request->getCardNumber(),
                             'Cvv2Val'  => $request->getSecurityCode(),
                             'Expires'  => $expireMonth,
                             'OrderId'  => $request->getOrderId(), );

        return $requestData;
    }
    
    /**
     * @see \Payment\Adapter\AdapterAbstract::_buildRefundRequest()
     */
    protected function _buildRefundRequest(Request $request)
    {
        $amount      = $this->_formatAmount($request->getAmount());
        $installment = $this->_formatInstallment($request->getInstallment());
        $currency    = $this->_formatCurrency($request->getCurrency());
        
        $requestData = array('Type'     => 'Credit',
                             'Total'    => $amount,
                             'Currency' => $currency,
                             'OrderId' => $request->getOrderId(), );

        return $requestData;
    }
    
    /**
     * @see \Payment\Adapter\AdapterAbstract::_buildCancelRequest()
     */
    protected function _buildCancelRequest(Request $request)
    {
        $requestData = array('Type'     => 'Void',
                             'OrderId' => $request->getOrderId(), );
        
        if( $request->getTransactionId() ) {
            $requestData['TransId'] = $request->getTransactionId();
        }

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
        $response->setIsSuccess( (string) $xml->Response == 'Approved' );
        $response->setResponseCode( (string) $xml->ProcReturnCode  );  
        if( ! $response->isSuccess() ) {
            $errorMessages = array();
            
            if( property_exists($xml, 'Error') ) {
                $errorMessages[] = sprintf('Error: %s', (string) $xml->Error);
            }
            
            if(property_exists($xml, 'ErrMsg')) {
                $errorMessages[] = sprintf('Error Message: %s ', 
                                            (string) $xml->ErrMsg);
            }
            
            if(property_exists($xml, 'Extra') && 
               property_exists($xml->Extra, 'HOSTMSG')) {
                $errorMessages[] = sprintf('Host Message: %s', 
                                           (string) $xml->Extra->HOSTMSG);
            }
            $errorMessage = implode(' ', $errorMessages);
            $response->setResponseMessage($errorMessage);
        } else {
            $response->setResponseMessage('Success');
            $response->setOrderId( (string) $xml->OrderId );
            $response->setTransactionId( (string) $xml->TransId );
        }
        $response->setRawData($rawResponse);
        return $response;
    }
}
