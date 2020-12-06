<?php
namespace Paranoia\Acquirer\Posnet\ResponseParser;

use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Model\Response\RefundResponse;

/**
 * Class RefundResponseParser
 * @package Paranoia\Acquirer\Posnet\ResponseParser
 */
class RefundResponseParser
{
    /** @var ResponseParserCommon */
    private $responseParserCommon;

    /**
     * RefundResponseParser constructor.
     * @param ResponseParserCommon $responseParserCommon
     */
    public function __construct(ResponseParserCommon $responseParserCommon)
    {
        $this->responseParserCommon = $responseParserCommon;
    }

    /**
     * @param string $rawResponse
     * @return RefundResponse
     * @throws BadResponseException
     */
    public function parse(string $rawResponse): RefundResponse
    {
        $xml = $this->responseParserCommon->parseResponse($rawResponse);
        $response = new RefundResponse();
        $this->responseParserCommon->decorateWithStatus($xml, $response);

        if (!$response->isApproved()) {
            $this->responseParserCommon->decorateWithErrorDetails($xml, $response);
        }

        return $response;
    }
}
