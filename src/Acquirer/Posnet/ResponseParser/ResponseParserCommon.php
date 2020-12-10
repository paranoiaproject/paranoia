<?php
namespace Paranoia\Acquirer\Posnet\ResponseParser;

use Paranoia\Core\Exception\BadResponseException;
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
        $response->setApproved((int)$xml->approved > 0);
    }

    /**
     * @param \SimpleXMLElement $xml
     * @param AbstractResponse $response
     */
    public function decorateWithErrorDetails(\SimpleXMLElement $xml, AbstractResponse $response): void
    {
        $errorMessages = array();

        if (property_exists($xml, 'respCode')) {
            $response->setErrorCode((string)$xml->respCode);
        }

        if (property_exists($xml, 'respText')) {
            $errorMessages[] = sprintf('%s ', (string)$xml->respText);
        }

        $errorMessage = implode(' ', $errorMessages);

        $response->setErrorMessage($errorMessage);
    }

    /**
     * @param \SimpleXMLElement $xml
     * @param PaymentResponse $response
     */
    public function decorateWithTransactionDetails(\SimpleXMLElement $xml, PaymentResponse $response): void
    {
        if (property_exists($xml, 'orderId')) {
            $response->setOrderId((string)$xml->orderId);
        }

        $response->setTransactionId((string)$xml->hostlogkey);

        if (property_exists($xml, 'authCode')) {
            $response->setAuthCode((string)$xml->authCode);
        }
    }
}
