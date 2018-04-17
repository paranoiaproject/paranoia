<?php
namespace Paranoia\Processor\Gvp;

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
    }

    /**
     * @param \SimpleXMLElement $xml
     * @param PaymentResponse $response
     */
    private function prepareTransactionDetails(\SimpleXMLElement $xml, PaymentResponse $response)
    {
        $response->setOrderId((string)$xml->Order->OrderID);
        $response->setTransactionId((string)$xml->Transaction->RetrefNum);
    }

    /**
     * @param $rawResponse
     * @return PaymentResponse
     * @throws BadResponseException
     */
    protected function processCommonResponse($rawResponse)
    {
        $response = new PaymentResponse();
        try {
            /** @var \SimpleXMLElement $xml */
            $xml = new \SimpleXmlElement($rawResponse);
        } catch (\Exception $e) {
            $exception = new BadResponseException('Provider returned unexpected response: ' . $rawResponse);
            throw $exception;
        }
        $this->validateResponse($xml);
        $response->setIsSuccess('00' == (string)$xml->Transaction->Response->Code);
        $response->setResponseCode((string)$xml->Transaction->ReasonCode);
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
