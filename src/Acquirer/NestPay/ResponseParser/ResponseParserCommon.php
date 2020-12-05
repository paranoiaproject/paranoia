<?php
namespace Paranoia\Acquirer\NestPay\ResponseParser;

use Paranoia\Acquirer\AbstractResponseParser;
use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Model\Response;
use Paranoia\Core\Model\Response\AbstractResponse;
use Paranoia\Core\Model\Response\PaymentResponse;

class ResponseParserCommon
{
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
        $response->setApproved((string)$xml->Response == 'Approved');
    }

    /**
     * @param \SimpleXMLElement $xml
     * @param AbstractResponse $response
     */
    public function decorateWithErrorDetails(\SimpleXMLElement $xml, AbstractResponse $response): void
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
        $response->setErrorCode((string) $xml->ProcReturnCode);
        $response->setErrorMessage($errorMessage);
    }

    /**
     * @param \SimpleXMLElement $xml
     * @param PaymentResponse $response
     */
    public function decorateWithTransactionDetails(\SimpleXMLElement $xml, PaymentResponse $response)
    {
        $response->setOrderId((string)$xml->OrderId);
        $response->setTransactionId((string)$xml->TransId);
        $response->setAuthCode((string) $xml->AuthCode);
    }
}
