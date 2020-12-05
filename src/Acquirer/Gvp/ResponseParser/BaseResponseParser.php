<?php
namespace Paranoia\Acquirer\Gvp\ResponseParser;

use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Acquirer\AbstractResponseParser;
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
     * @param Response $response
     */
    private function prepareTransactionDetails(\SimpleXMLElement $xml, Response $response)
    {
        if (property_exists($xml, 'Order') && property_exists($xml->Order, 'OrderID')) {
            $response->setOrderId((string)$xml->Order->OrderID);
        }

        if (property_exists($xml, 'Transaction')) {
            if (property_exists($xml->Transaction, 'RetrefNum')) {
                $response->setTransactionId((string)$xml->Transaction->RetrefNum);
            }

            if (property_exists($xml->Transaction, 'AuthCode')) {
                $response->setAuthCode((string)$xml->Transaction->AuthCode);
            }
        }
    }

    /**
     * @param $rawResponse
     * @return Response
     * @throws BadResponseException
     */
    protected function processCommonResponse($rawResponse)
    {
        $response = new Response();
        try {
            /** @var \SimpleXMLElement $xml */
            $xml = new \SimpleXmlElement($rawResponse);
        } catch (\Exception $e) {
            $exception = new BadResponseException('Provider returned unexpected response: ' . $rawResponse);
            throw $exception;
        }
        $this->validateResponse($xml);
        $response->setIsSuccess('00' == (string)$xml->Transaction->Response->Code);
        $response->setResponseCode((string)$xml->Transaction->Response->ReasonCode);
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
