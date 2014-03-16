<?php
namespace Paranoia\Payment\Adapter;

use Paranoia\Common\Serializer\Serializer;
use Paranoia\Payment\Request;
use Paranoia\Payment\Response\PaymentResponse;
use Paranoia\Payment\Exception\UnexpectedResponse;
use Paranoia\Communication\Connector;

class Gvp extends AdapterAbstract implements AdapterInterface
{

    const CONNECTOR_TYPE = Connector::CONNECTOR_TYPE_HTTP;

    /**
     * @var array
     */
    protected $_transactionMap = array(
        self::TRANSACTION_TYPE_PREAUTHORIZATION  => 'preauth',
        self::TRANSACTION_TYPE_POSTAUTHORIZATION => 'postauth',
        self::TRANSACTION_TYPE_SALE              => 'sales',
        self::TRANSACTION_TYPE_CANCEL            => 'void',
        self::TRANSACTION_TYPE_REFUND            => 'refund'
    );

    /**
     * builds request base with common arguments.
     *
     * @param Request $request
     *
     * @return array
     */
    private function _buildBaseRequest( Request $request )
    {
        $terminal    = $this->_buildTerminal($request);
        $customer    = $this->_buildCustomer($request);
        $order       = $this->_buildOrder($request);
        $transaction = $this->_buildTransaction($request);
        return array(
            'Version'     => '0.01',
            'Mode'        => $this->getConfiguration()->getMode(),
            'Terminal'    => $terminal,
            'Order'       => $order,
            'Customer'    => $customer,
            'Transaction' => $transaction
        );
    }

    /**
     * builds terminal section of request.
     *
     * @param Request $request
     *
     * @return array
     */
    private function _buildTerminal( Request $request )
    {
        list( $username, $password ) = $this->_getApiCredentialsByRequest($request->getTransactionType());
        $hash = $this->_getTransactionHash($request, $password);
        return array(
            'ProvUserID' => $username,
            'HashData'   => $hash,
            'UserID'     => $username,
            'ID'         => $this->getConfiguration()->getTerminalId(),
            'MerchantID' => $this->getConfiguration()->getMerchantId()
        );
    }

    /**
     * builds customer section of request.
     *
     * @param Request $request
     *
     * @return array
     */
    private function _buildCustomer( Request $request )
    {
        /**
         * we don't want to share customer information
         * to bank.
         */
        return array(
            'IPAddress'    => '127.0.0.1',
            'EmailAddress' => 'dummy@dummy.net'
        );
    }

    /**
     * builds card section of request.
     *
     * @param Request $request
     *
     * @return array
     */
    private function _buildCard( Request $request )
    {
        $expireMonth = $this->_formatExpireDate($request->getExpireMonth(), $request->getExpireYear());
        return array(
            'Number'     => $request->getCardNumber(),
            'ExpireDate' => $expireMonth,
            'CVV2'       => $request->getSecurityCode()
        );
    }

    /**
     * builds order section of request.
     *
     * @param Request $request
     *
     * @return array
     */
    private function _buildOrder( Request $request )
    {
        return array(
            'OrderID'     => $request->getOrderId(),
            'GroupID'     => null,
            'Description' => null
        );
    }

    /**
     * builds terminal section of request.
     *
     * @param Request $request
     * @param integer $cardHolderPresentCode
     * @param string  $originalRetrefNum
     *
     * @return array
     */
    private function _buildTransaction( Request $request, $cardHolderPresentCode = 0, $originalRetrefNum = null )
    {
        $transactionType = $request->getTransactionType();
        $installment     = $this->_formatInstallment($request->getInstallment());
        $amount          = $this->_isAmountRequired($request) ? $this->_formatAmount($request->getAmount()) : '1';
        $currency        = $this->_formatCurrency($request->getCurrency());
        $type            = $this->_getProviderTransactionType($transactionType);
        return array(
            'Type'                  => $type,
            'InstallmentCnt'        => $installment,
            'Amount'                => $amount,
            'CurrencyCode'          => $currency,
            'CardholderPresentCode' => $cardHolderPresentCode,
            'MotoInd'               => 'N',
            'OriginalRetrefNum'     => $originalRetrefNum
        );
    }

    /**
     * returns boolean true, when amount field is required
     * for request transaction type.
     *
     * @param Request $request
     *
     * @return boolean
     */
    private function _isAmountRequired( Request $request )
    {
        return in_array(
            $request->getTransactionType(),
            array(
                self::TRANSACTION_TYPE_SALE,
                self::TRANSACTION_TYPE_PREAUTHORIZATION,
                self::TRANSACTION_TYPE_POSTAUTHORIZATION,
            )
        );
    }

    /**
     * returns boolean true, when card number field is required
     * for request transaction type.
     *
     * @param Request $request
     *
     * @return boolean
     */
    private function _isCardNumberRequired( Request $request )
    {
        return in_array(
            $request->getTransactionType(),
            array(
                self::TRANSACTION_TYPE_SALE,
                self::TRANSACTION_TYPE_PREAUTHORIZATION,
            )
        );
    }

    /**
     * returns api credentials by transaction type of request.
     *
     * @param string $transactionType
     *
     * @return array
     */
    private function _getApiCredentialsByRequest( $transactionType )
    {
        $isAuth = in_array(
            $transactionType,
            array(
                self::TRANSACTION_TYPE_SALE,
                self::TRANSACTION_TYPE_PREAUTHORIZATION,
                self::TRANSACTION_TYPE_POSTAUTHORIZATION,
            )
        );

        if ($isAuth) {
            return array( $this->getConfiguration()->getAuthorizationUsername(), $this->getConfiguration()->getAuthorizationPassword() );
        } else {
            return array( $this->getConfiguration()->getRefundUsername(), $this->getConfiguration()->getRefundPassword() );
        }
    }

    /**
     * returns security hash for using in transaction hash.
     *
     * @param string $password
     *
     * @return string
     */
    private function _getSecurityHash( $password )
    {
        $tidPrefix  = str_repeat('0', 9 - strlen($this->getConfiguration()->getTerminalId()));
        $terminalId = sprintf('%s%s', $tidPrefix, $this->getConfiguration()->getTerminalId());
        return strtoupper(SHA1(sprintf('%s%s', $password, $terminalId)));
    }

    /**
     * returns transaction hash for using in transaction request.
     *
     * @param Request $request
     * @param string  $password
     *
     * @return string
     */
    private function _getTransactionHash( Request $request, $password )
    {
        $orderId      = $request->getOrderId();
        $terminalId   = $this->getConfiguration()->getTerminalId();
        $cardNumber   = $this->_isCardNumberRequired($request) ? $request->getCardNumber() : '';
        $amount       = $this->_isAmountRequired($request) ? $this->_formatAmount($request->getAmount()) : '1';
        $securityData = $this->_getSecurityHash($password);
        return strtoupper(
            sha1(
                sprintf(
                    '%s%s%s%s%s',
                    $orderId,
                    $terminalId,
                    $cardNumber,
                    $amount,
                    $securityData
                )
            )
        );
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::_buildRequest()
     */
    protected function _buildRequest( Request $request, $requestBuilder )
    {
        $rawRequest = call_user_func(array( $this, $requestBuilder ), $request);
        $serializer = new Serializer( Serializer::XML );
        $xml        = $serializer->serialize(
                                 $rawRequest,
                                     array( 'root_name' => 'GVPSRequest' )
        );
        $data       = array( 'data' => $xml );
        $request->setRawData($xml);
        return http_build_query($data);
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::_buildPreauthorizationRequest()
     */
    protected function _buildPreauthorizationRequest( Request $request )
    {
        $requestData = array( 'Card' => $this->_buildCard($request) );
        return array_merge($requestData, $this->_buildBaseRequest($request));
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::_buildPostAuthorizationRequest()
     */
    protected function _buildPostAuthorizationRequest( Request $request )
    {
        $requestData = $this->_buildBaseRequest($request);
        return $requestData;
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::_buildSaleRequest()
     */
    protected function _buildSaleRequest( Request $request )
    {
        $requestData = array( 'Card' => $this->_buildCard($request) );
        return array_merge($requestData, $this->_buildBaseRequest($request));
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::_buildRefundRequest()
     */
    protected function _buildRefundRequest( Request $request )
    {
        return $this->_buildBaseRequest($request);
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::_buildCancelRequest()
     */
    protected function _buildCancelRequest( Request $request )
    {
        $requestData                = $this->_buildBaseRequest($request);
        $transactionId              = ( $request->getTransactionId() ) ? $request->getTransactionId() : null;
        $transaction                = $this->_buildTransaction($request, 0, $transactionId);
        $requestData['Transaction'] = $transaction;
        return $requestData;
    }

    /**
     * @see      Paranoia\Payment\Adapter\AdapterAbstract::_parseResponse()
     */
    protected function _parseResponse( $rawResponse )
    {
        $response = new PaymentResponse();
        try {
            /**
             * @var object $xml
             */
            $xml = new \SimpleXmlElement( $rawResponse );
        } catch ( \Exception $e ) {
            throw new UnexpectedResponse( 'Provider is returned unexpected response. Response data:' . $rawResponse );
        }
        $response->setIsSuccess((string)$xml->Transaction->Response->Code == '00');
        $response->setResponseCode((string)$xml->Transaction->ReasonCode);
        if (!$response->isSuccess()) {
            $errorMessages = array();
            if (property_exists($xml->Transaction->Response, 'ErrorMsg')) {
                $errorMessages[] = sprintf(
                    'Error Message: %s',
                    (string)$xml->Transaction->Response->ErrorMsg
                );
            }
            if (property_exists($xml->Transaction->Response, 'SysErrMsg')) {
                $errorMessages[] = sprintf(
                    'System Error Message: %s',
                    (string)$xml->Transaction->Response->SysErrMsg
                );
            }
            $errorMessage = implode(' ', $errorMessages);
            $response->setResponseMessage($errorMessage);
        } else {
            $response->setResponseMessage('Success');
            $response->setOrderId((string)$xml->Order->OrderID);
            $response->setTransactionId((string)$xml->Transaction->RetrefNum);
        }
        $response->setRawData($rawResponse);
        return $response;
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::_formatAmount()
     */
    protected function _formatAmount( $amount, $reverse = false )
    {
        return ( !$reverse ) ? number_format($amount, 2, '', '') : (float)substr($amount, 0, -2) . '.' . substr(
                $amount,
                -2
            );
    }

    /**
     * @see Paranoia\Payment\Adapter\AdapterAbstract::_formatExpireDate()
     */
    protected function _formatExpireDate( $month, $year )
    {
        return sprintf('%02s%s', $month, substr($year, -2));
    }
}
