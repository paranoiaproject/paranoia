<?php
namespace Paranoia\Payment\Adapter;

use Paranoia\Common\Serializer\Serializer;
use Paranoia\Payment\PaymentEventArg;
use Paranoia\Payment\Request;
use Paranoia\Payment\Response\PaymentResponse;
use Paranoia\Payment\Exception\UnexpectedResponse;
use Paranoia\Payment\Exception\UnimplementedMethod;

class Iyzico extends AdapterAbstract
{
    /**
     * @var array
     */
    protected $transactionMap = array(
        self::TRANSACTION_TYPE_PREAUTHORIZATION  => 'PA',
        self::TRANSACTION_TYPE_POSTAUTHORIZATION => 'CP',
        self::TRANSACTION_TYPE_SALE              => 'DB',
        self::TRANSACTION_TYPE_CANCEL            => 'RV',
        self::TRANSACTION_TYPE_REFUND            => 'RF'
    );

    /**
     * builds request base with common arguments.
     *
     * @param Request $request
     * @param string $transactionType
     *
     * @return array
     */
    private function buildBaseRequest(Request $request, $transactionType)
    {
      
        return array(
            'response_mode'     => 'SYNC',
            'mode'        => $this->configuration->getMode(),
            'api_id'    => $this->configuration->getApiId(),
            'secret'    => $this->configuration->getApiSecret(),
            'type' => $transactionType
        );
    }

   

    /**
     * builds card section of request.
     *
     * @param Request $request
     *
     * @return array
     */
    private function buildCard(Request $request)
    {
        $expireDate = $this->formatExpireDate(
            $request->getExpireMonth(),
            $request->getExpireYear()
        );
        return array(
            'card_number'     => $request->getCardNumber(),
            'card_expiry_month' => $expireDate['month'],
            'card_expiry_year' => $expireDate['year'],
            'card_verification' => $request->getSecurityCode(),
            'card_holder_name' => '',
            'card_brand' => $this->findCardBrand($request->getCardNumber())
        );
    }


 
    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildRequest()
     */
    protected function buildRequest(Request $request, $requestBuilder)
    {
   
        $rawRequest = call_user_func(array( $this, $requestBuilder ), $request);
        return $rawRequest;
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildSaleRequest()
     */
    protected function buildSaleRequest(Request $request)
    {
        $base = $this->buildBaseRequest($request, $this->getProviderTransactionType(self::TRANSACTION_TYPE_SALE));
        $amount      = $this->formatAmount($request->getAmount());
        $currency    = $this->formatCurrency($request->getCurrency());
        $requestData = array_merge(
            $base,
            array_merge(
                $this->buildCard($request),
                array(
                    'amount'    => $amount,
                    'currency' => $currency,
                    'external_id' => $this->formatOrderId($request->getOrderId())
                )
            )
        );
        return $requestData;
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildPreauthorizationRequest()
     */
    protected function buildPreAuthorizationRequest(Request $request)
    {

        $base = $this->buildBaseRequest(
            $request,
            $this->getProviderTransactionType(self::TRANSACTION_TYPE_PREAUTHORIZATION)
        );
        $amount      = $this->formatAmount($request->getAmount());
        $currency    = $this->formatCurrency($request->getCurrency());
        $requestData = array_merge(
            $base,
            array_merge(
                $this->buildCard($request),
                array(
                    'amount'    => $amount,
                    'currency' => $currency,
                    'external_id' => $this->formatOrderId($request->getOrderId())
                )
            )
        );
        return $requestData;
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildPostAuthorizationRequest()
     */
    protected function buildPostAuthorizationRequest(Request $request)
    {
        $base = $this->buildBaseRequest(
            $request,
            $this->getProviderTransactionType(self::TRANSACTION_TYPE_POSTAUTHORIZATION)
        );
        $amount      = $this->formatAmount($request->getAmount());
        $currency    = $this->formatCurrency($request->getCurrency());
        $requestData = array_merge(
            $base,
            array_merge(
                $this->buildCard($request),
                array(
                    'amount'    => $amount,
                    'currency' => $currency,
                    'external_id' => $this->formatOrderId($request->getOrderId()),
                    'transaction_id' => $request->getTransactionId()
                )
            )
        );
        return $requestData;
    }


    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildRefundRequest()
     */
    protected function buildRefundRequest(Request $request)
    {
        $base = $this->buildBaseRequest($request, $this->getProviderTransactionType(self::TRANSACTION_TYPE_REFUND));
        $amount      = $this->formatAmount($request->getAmount());
        $currency    = $this->formatCurrency($request->getCurrency());
        $requestData = array_merge(
            $base,
            array_merge(
                $this->buildCard($request),
                array(
                    'amount'    => $amount,
                    'currency' => $currency,
                    'external_id' => $this->formatOrderId($request->getOrderId()),
                    'transaction_id' => $request->getTransactionId()
                )
            )
        );
        return $requestData;
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildCancelRequest()
     */
    protected function buildCancelRequest(Request $request)
    {
        $requestData = $this->buildRefundRequest($request);
        $requestData["type"] = $this->getProviderTransactionType(self::TRANSACTION_TYPE_CANCEL);
        return $requestData;
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::parseResponse()
     */
    protected function buildPointQueryRequest(Request $request)
    {
        throw new UnimplementedMethod();
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::buildPointUsageRequest()
     */
    protected function buildPointUsageRequest(Request $request)
    {
        throw new UnimplementedMethod();
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::parseResponse()
     */
    protected function parseResponse($rawResponse, $transactionType)
    {
         
        $response = new PaymentResponse();
        try {
            $json = json_decode(strval($rawResponse));
        } catch ( \Exception $e ) {
            $exception = new UnexpectedResponse('Provider returned unexpected response: ' . $rawResponse);
            $eventArg = new PaymentEventArg(null, null, $transactionType, $exception);
            $this->getDispatcher()->dispatch(self::EVENT_ON_EXCEPTION, $eventArg);
            throw $exception;
        }
        $response->setIsSuccess('success' == (string)$json->response->state);
        if (!$response->isSuccess()) {
            if (property_exists($json->response, 'error_message')) {
                $response->setResponseMessage(sprintf(
                    'Error Message: %s',
                    (string)$json->response->error_message
                ));

            }
            $response->setResponseCode((string)$json->response->error_code);
        } else {
            $response->setResponseMessage('Success');
            $response->setOrderId((string)$json->transaction->external_id);
            $response->setTransactionId((string)$json->transaction->transaction_id);
        }
        $response->setTransactionType($transactionType);
        $event = $response->isSuccess() ? self::EVENT_ON_TRANSACTION_SUCCESSFUL : self::EVENT_ON_TRANSACTION_FAILED;
        $this->getDispatcher()->dispatch($event, new PaymentEventArg(null, $response, $transactionType));
        return $response;
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::formatAmount()
     */
    protected function formatAmount($amount, $reverse = false)
    {
            return strval(number_format($amount, 2, '.', '')*100);
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::formatCurrency()
     */
    protected function formatCurrency($currency)
    {
            return $currency;
    }

    /**
     * {@inheritdoc}
     * @see Paranoia\Payment\Adapter\AdapterAbstract::formatExpireDate()
     */
    protected function formatExpireDate($month, $year)
    {
        return array(
                'month' => sprintf('%02s', $month),
                'year'  => sprintf('%02s', substr($year, -2))
             );
    }

    /**
     * Finds appropriate card brand depending on card number
     *
     * @param string $cardNumber
     *
     * @return string
     */
    protected function findCardBrand($cardNumber)
    {
        $brand = "Invalid";
        $digitLength = strlen($cardNumber);
        switch ($digitLength) {
            case 15:
                if (substr($cardNumber, 0, 2) == "34" || substr($cardNumber, 0, 2) == "37") {
                    $brand = "AMEX";
                }

                break;
            case 13:
                if (substr($cardNumber, 0, 1) == "4") {
                    $brand = "VISA";
                }

                break;
            case 16:
                if (substr($cardNumber, 0, 1) == "4") {
                    $brand = "VISA";
                } elseif (substr($cardNumber, 0, 4) == "6011") {
                    $brand = "DISCOVER";
                } elseif (intval(substr($cardNumber, 0, 2)) >= 51 && intval(substr($cardNumber, 0, 2)) <= 55) {
                    $brand = "MASTER";
                }

                break;
           
        }

        return $brand;
    }
}
