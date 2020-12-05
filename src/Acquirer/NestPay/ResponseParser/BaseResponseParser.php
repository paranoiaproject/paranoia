<?php
namespace Paranoia\Acquirer\NestPay\ResponseParser;

use Paranoia\Acquirer\AbstractResponseParser;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Model\Response;

abstract class BaseResponseParser extends AbstractResponseParser
{
    /**
     * @param \SimpleXMLElement $xml
     * @param Response $response
     */
    private function prepareErrorDetails(\SimpleXMLElement $xml, Response $response)
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
     * @param Response $response
     */
    private function prepareTransactionDetails(\SimpleXMLElement $xml, Response $response)
    {
        $response->setOrderId((string)$xml->OrderId);
        $response->setTransactionId((string)$xml->TransId);
        $response->setAuthCode((string) $xml->AuthCode);
    }

    /**
     * @param $rawResponse
     * @return Response
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
        $response = new Response();
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
