<?php
namespace Paranoia\Acquirer\Posnet\ResponseParser;

use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Model\Response\ChargeResponse;

/**
 * Class ChargeResponseParser
 * @package Paranoia\Acquirer\Posnet\ResponseParser
 */
class ChargeResponseParser
{
    /** @var ResponseParserCommon */
    private $responseParserCommon;

    /**
     * ChargeResponseParser constructor.
     * @param ResponseParserCommon $responseParserCommon
     */
    public function __construct(ResponseParserCommon $responseParserCommon)
    {
        $this->responseParserCommon = $responseParserCommon;
    }

    /**
     * @param string $rawResponse
     * @return ChargeResponse
     * @throws BadResponseException
     */
    public function parse(string $rawResponse): ChargeResponse
    {
        $xml = $this->responseParserCommon->parseResponse($rawResponse);
        $response = new ChargeResponse();
        $this->responseParserCommon->decorateWithStatus($xml, $response);

        if ($response->isApproved()) {
            $this->responseParserCommon->decorateWithTransactionDetails($xml, $response);
        } else {
            $this->responseParserCommon->decorateWithErrorDetails($xml, $response);
        }

        return $response;
    }
}
