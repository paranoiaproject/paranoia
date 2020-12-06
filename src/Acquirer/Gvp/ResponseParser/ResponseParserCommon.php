<?php
namespace Paranoia\Acquirer\Gvp\ResponseParser;

use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Model\Response\AbstractResponse;
use Paranoia\Core\Model\Response\PaymentResponse;

class ResponseParserCommon
{
    private const SUCCESSFUL_RESPONSE_CODE = '00';

    /**
     * @param string $rawResponse
     * @return \SimpleXMLElement
     * @throws BadResponseException
     */
    public function parseResponse(string $rawResponse): \SimpleXMLElement
    {
        try {
            /** @var \SimpleXMLElement $xml */
            return new \SimpleXmlElement($rawResponse);
        } catch (\Exception $e) {
            throw new BadResponseException('Provider returned unexpected response: ' . $rawResponse);
        }
    }

    /**
     * @param \SimpleXMLElement $xml
     * @param AbstractResponse $response
     */
    public function decorateWithStatus(\SimpleXMLElement $xml, AbstractResponse $response): void
    {
        $response->setApproved(self::SUCCESSFUL_RESPONSE_CODE == (string)$xml->Transaction->Response->Code);
    }

    /**
     * @param \SimpleXMLElement $xml
     * @param AbstractResponse $response
     */
    public function decorateWithErrorDetails(\SimpleXMLElement $xml, AbstractResponse $response): void
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
        $response->setErrorCode((string) $xml->Transaction->Response->ReasonCode);
        $response->setErrorMessage($errorMessage);
    }

    /**
     * @param \SimpleXMLElement $xml
     * @param PaymentResponse $response
     */
    public function decorateWithTransactionDetails(\SimpleXMLElement $xml, PaymentResponse $response): void
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
}
