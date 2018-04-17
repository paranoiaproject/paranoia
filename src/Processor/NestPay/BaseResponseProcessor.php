<?php
namespace Paranoia\Processor\NestPay;

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
    }

    /**
     * @param \SimpleXMLElement $xml
     * @param PaymentResponse $response
     */
    private function prepareTransactionDetails(\SimpleXMLElement $xml, PaymentResponse $response)
    {
        $response->setOrderId((string)$xml->OrderId);
        $response->setTransactionId((string)$xml->TransId);
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
        $response->setIsSuccess((string)$xml->Response == 'Approved');
        $response->setResponseCode((string)$xml->ProcReturnCode);
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
