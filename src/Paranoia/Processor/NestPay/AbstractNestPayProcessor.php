<?php
namespace Paranoia\Processor\NestPay;

use Paranoia\Exception\BadResponseError;
use Paranoia\Processor\AbstractProcessor;
use Paranoia\Transfer\Response\ResponseInterface;

abstract class AbstractNestPayProcessor extends AbstractProcessor
{
    /**
     * @param \SimpleXMLElement $xml
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    private function prepareServiceInfo(\SimpleXMLElement $xml, ResponseInterface $response)
    {
        $response->setIsSuccess((string)$xml->Response == 'Approved');
        $response->setCode((string)$xml->ProcReturnCode);
        return $response;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    private function prepareServiceErrorInfo(\SimpleXMLElement $xml, ResponseInterface $response)
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
        $response->setMessage($errorMessage);

        return $response;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    private function prepareTransactionInfo(\SimpleXMLElement $xml, ResponseInterface $response)
    {
        $response->setOrderId((string)$xml->OrderId);
        $response->setTransactionId((string)$xml->TransId);
        return $response;
    }

    /**
     * @param $rawResponse
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws BadResponseError
     */
    protected function prepareResponse($rawResponse, ResponseInterface $response)
    {
        try {
            $xml = new \SimpleXMLElement($rawResponse);
        } catch (\Exception $e) {
            $errorMessage = 'Provider returned unexpected ' .
                            'response. Response detail: ' . $rawResponse;
            throw new BadResponseError($errorMessage);
        }

        $this->prepareServiceInfo($xml, $response);
        if(!$response->isSuccess()) {
            $this->prepareServiceErrorInfo($xml, $response);
        } else {
            $this->prepareTransactionInfo($xml, $response);
        }

        return $response;
    }
}