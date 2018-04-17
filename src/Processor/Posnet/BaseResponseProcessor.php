<?php
namespace Paranoia\Processor\Posnet;

use Paranoia\Exception\BadResponseException;
use Paranoia\Processor\AbstractResponseProcessor;
use Paranoia\Response\PaymentResponse;

abstract class BaseResponseProcessor extends AbstractResponseProcessor
{
    /**
     * @param \SimpleXMLElement $xml
     * @param PaymentResponse $response
     */
    private function prepareErrorDetails(\SimpleXMLElement $xml, PaymentResponse $response)
    {
        $response->setResponseCode((string)$xml->respCode);
        $errorMessages = array();
        if (property_exists($xml, 'respCode')) {
            $errorMessages[] = sprintf('Error: %s', (string)$xml->respCode);
        }
        if (property_exists($xml, 'respText')) {
            $errorMessages[] = sprintf('Error Message: %s ', (string)$xml->respText);
        }
        $errorMessage = implode(' ', $errorMessages);
        $response->setResponseMessage($errorMessage);
    }

    /**
     * @param \SimpleXMLElement $xml
     * @param PaymentResponse $response
     */
    private function prepareTransactionDetails(\SimpleXMLElement $xml, PaymentResponse $response)
    {
        if (property_exists($xml, 'orderId')) {
            $response->setOrderId((string)$xml->orderId);
        }
        $response->setTransactionId((string)$xml->hostlogkey);
        if (property_exists($xml, 'authCode')) {
            $response->setOrderId((string)$xml->authCode);
        }
    }

    /**
     * @param $rawResponse
     * @return PaymentResponse
     * @throws BadResponseException
     */
    protected function processCommonResponse($rawResponse)
    {
        try {
            /** @var \SimpleXMLElement $xml */
            $xml = new \SimpleXmlElement($rawResponse);
        } catch (\Exception $e) {
            $exception = new BadResponseException('Provider returned unexpected response: ' . $rawResponse);
            throw $exception;
        }
        $this->validateResponse($xml);
        $response = new PaymentResponse();
        $response->setIsSuccess((int)$xml->approved > 0);
        if (!$response->isSuccess()) {
            $this->prepareErrorDetails($xml, $response);
        } else {
            $this->prepareTransactionDetails($xml, $response);
        }
        return $response;
    }

    protected function validateResponse($transformedResponse)
    {
        //TODO: response validation should implemented.
        return true;
    }
}
