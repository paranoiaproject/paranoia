<?php
namespace Payment\Adapter;

use \Array2XML;

use \Payment\Request;
use \Payment\Response\PaymentResponse;

use \Payment\Adapter\AdapterInterface;
use \Payment\Adapter\AdapterAbstract;

use \Payment\Exception\UnexpectedResponse;

use \Communication\Connector;

class Posnet extends AdapterAbstract implements AdapterInterface
{
    const CONNECTOR_TYPE =  Connector::CONNECTOR_TYPE_HTTP;

    private function buildBaseRequest()
    {

    }

    /**
     * @see \Payment\Adapter\AdapterAbstract::_buildRequest()
     */
    protected function _buildRequest(Request $request, $requestBuilder)
    {

    }

    /**
     * @see \Payment\Adapter\AdapterAbstract::_buildPreauthorizationRequest()
     */
    protected function _buildPreauthorizationRequest(Request $request)
    {

    }

    /**
     * @see \Payment\Adapter\AdapterAbstract::_buildPostAuthorizationRequest()
     */
    protected function _buildPostAuthorizationRequest(Request $request)
    {

    }

    /**
     * @see \Payment\Adapter\AdapterAbstract::_buildSaleRequest()
     */
    protected function _buildSaleRequest(Request $request)
    {

    }

    /**
     * @see \Payment\Adapter\AdapterAbstract::_buildRefundRequest()
     */
    protected function _buildRefundRequest(Request $request)
    {

    }

    /**
     * @see \Payment\Adapter\AdapterAbstract::_buildCancelRequest()
     */
    protected function _buildCancelRequest(Request $request)
    {

    }

    /**
     * @see \Payment\Adapter\AdapterAbstract::_parseResponse()
     */
    protected function _parseResponse($rawResponse)
    {

    }

    protected function _formatCurrency($currency)
    {
      switch($currency) {
        case self::CURRENCY_TRL:
          return 'TR';
        case self::CURRENCY_USD:
          return 'US';
        case self::CURRENCY_EUR:
          return 'EU';
        default:
          return 'TR';
      }
    }
}
