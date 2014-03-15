<?php
namespace Paranoia\Payment\Adapter;

use Paranoia\Common\Serializer\Serializer;
use Paranoia\Communication\Connector;
use Paranoia\Payment\Request;
use Paranoia\Payment\Response\PaymentResponse;
use Paranoia\Payment\Adapter\AdapterInterface;
use Paranoia\Payment\Adapter\AdapterAbstract;
use Paranoia\Payment\Exception\UnexpectedResponse;

class Est extends AdapterAbstract implements AdapterInterface
{

    const CONNECTOR_TYPE = Connector::CONNECTOR_TYPE_HTTP;

    /**
     * @var array
     */
    protected $_transactionMap = array(
        self::TRANSACTION_TYPE_PREAUTHORIZATION  => 'PreAuth',
        self::TRANSACTION_TYPE_POSTAUTHORIZATION => 'PostAuth',
        self::TRANSACTION_TYPE_SALE              => 'Auth',
        self::TRANSACTION_TYPE_CANCEL            => 'Void',
        self::TRANSACTION_TYPE_REFUND            => 'Credit'
    );

    /**
     * builds request base with common arguments.
     *
     * @return array
     */
    private function _buildBaseRequest()
    {
        $config = $this->_config;
        return array(
            'Name'     => $config->username,
            'Password' => $config->password,
            'ClientId' => $config->client_id,
            'Mode'     => $config->mode
        );
    }

    /**
     * @see AdapterAbstract::_buildRequest()
     */
    protected function _buildRequest( Request $request, $requestBuilder )
    {
        $rawRequest = call_user_func(array( $this, $requestBuilder ), $request);
        $serializer = new Serializer( Serializer::XML );
        $xml        = $serializer->serialize(
                                 array_merge($rawRequest, $this->_buildBaseRequest()),
                                     array( 'root_name' => 'CC5Request' )
        );
        $data       = array( 'DATA' => $xml );
        $request->setRawData($xml);
        return http_build_query($data);
    }

    /**
     * @see AdapterAbstract::_buildPreauthorizationRequest()
     */
    protected function _buildPreauthorizationRequest( Request $request )
    {
        $amount      = $this->_formatAmount($request->getAmount());
        $installment = $this->_formatInstallment($request->getInstallment());
        $currency    = $this->_formatCurrency($request->getCurrency());
        $expireMonth = $this->_formatExpireDate($request->getExpireMonth(),$request->getExpireYear());
        $type        = $this->_getProviderTransactionType($request->getTransactionType());
        $requestData = array(
            'Type'     => $type,
            'Total'    => $amount,
            'Currency' => $currency,
            'Taksit'   => $installment,
            'Number'   => $request->getCardNumber(),
            'Cvv2Val'  => $request->getSecurityCode(),
            'Expires'  => $expireMonth,
            'OrderId'  => $request->getOrderId(),
        );
        return $requestData;
    }

    /**
     * @see AdapterAbstract::_buildPostAuthorizationRequest()
     */
    protected function _buildPostAuthorizationRequest( Request $request )
    {
        $type        = $this->_getProviderTransactionType($request->getTransactionType());
        $requestData = array(
            'Type'    => $type,
            'OrderId' => $request->getOrderId(),
        );
        return $requestData;
    }

    /**
     * @see AdapterAbstract::_buildSaleRequest()
     */
    protected function _buildSaleRequest( Request $request )
    {
        $amount      = $this->_formatAmount($request->getAmount());
        $installment = $this->_formatInstallment($request->getInstallment());
        $currency    = $this->_formatCurrency($request->getCurrency());
        $expireMonth = $this->_formatExpireDate($request->getExpireMonth(),$request->getExpireYear());
        $type        = $this->_getProviderTransactionType($request->getTransactionType());
        $requestData = array(
            'Type'     => $type,
            'Total'    => $amount,
            'Currency' => $currency,
            'Taksit'   => $installment,
            'Number'   => $request->getCardNumber(),
            'Cvv2Val'  => $request->getSecurityCode(),
            'Expires'  => $expireMonth,
            'OrderId'  => $request->getOrderId(),
        );
        return $requestData;
    }

    /**
     * @see AdapterAbstract::_buildRefundRequest()
     */
    protected function _buildRefundRequest( Request $request )
    {
        $amount      = $this->_formatAmount($request->getAmount());
        $currency    = $this->_formatCurrency($request->getCurrency());
        $type        = $this->_getProviderTransactionType($request->getTransactionType());
        $requestData = array(
            'Type'     => $type,
            'Total'    => $amount,
            'Currency' => $currency,
            'OrderId'  => $request->getOrderId(),
        );
        return $requestData;
    }

    /**
     * @see AdapterAbstract::_buildCancelRequest()
     */
    protected function _buildCancelRequest( Request $request )
    {
        $type        = $this->_getProviderTransactionType($request->getTransactionType());
        $requestData = array(
            'Type'    => $type,
            'OrderId' => $request->getOrderId(),
        );
        if ($request->getTransactionId()) {
            $requestData['TransId'] = $request->getTransactionId();
        }
        return $requestData;
    }

    /**
     * @see AdapterAbstract::_parseResponse()
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
            $exception = new UnexpectedResponse( 'Provider is returned unexpected ' . 'response. Response data:' . $rawResponse );
            $this->_triggerEvent(
                 self::EVENT_ON_EXCEPTION,
                     array_merge(
                         $this->_collectTransactionInformation(),
                         array( 'exception' => $exception )
                     )
            );
            throw $exception;
        }
        $response->setIsSuccess((string)$xml->Response == 'Approved');
        $response->setResponseCode((string)$xml->ProcReturnCode);
        if (!$response->isSuccess()) {
            $errorMessages = array();
            if (property_exists($xml, 'Error')) {
                $errorMessages[] = sprintf('Error: %s', (string)$xml->Error);
            }
            if (property_exists($xml, 'ErrMsg')) {
                $errorMessages[] = sprintf(
                    'Error Message: %s ',
                    (string)$xml->ErrMsg
                );
            }
            if (property_exists($xml, 'Extra') && property_exists($xml->Extra, 'HOSTMSG')) {
                $errorMessages[] = sprintf(
                    'Host Message: %s',
                    (string)$xml->Extra->HOSTMSG
                );
            }
            $errorMessage = implode(' ', $errorMessages);
            $response->setResponseMessage($errorMessage);
        } else {
            $response->setResponseMessage('Success');
            $response->setOrderId((string)$xml->OrderId);
            $response->setTransactionId((string)$xml->TransId);
        }
        $response->setRawData($rawResponse);
        $eventData = $this->_collectTransactionInformation();
        $eventName = $response->isSuccess() ? self::EVENT_ON_TRANSACTION_SUCCESSFUL : self::EVENT_ON_TRANSACTION_FAILED;
        $this->_triggerEvent($eventName, $eventData);
        return $response;
    }
}
