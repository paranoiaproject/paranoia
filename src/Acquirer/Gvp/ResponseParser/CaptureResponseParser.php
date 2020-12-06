<?php
namespace Paranoia\Acquirer\Gvp\ResponseParser;

use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Model\Response\CaptureResponse;

/**
 * Class CaptureResponseParser
 * @package Paranoia\Acquirer\Gvp\ResponseParser
 */
class CaptureResponseParser
{
    /** @var ResponseParserCommon */
    private $responseParserCommon;

    /**
     * CaptureResponseParser constructor.
     * @param ResponseParserCommon $responseParserCommon
     */
    public function __construct(ResponseParserCommon $responseParserCommon)
    {
        $this->responseParserCommon = $responseParserCommon;
    }

    /**
     * @param string $rawResponse
     * @return CaptureResponse
     * @throws BadResponseException
     */
    public function parse(string $rawResponse): CaptureResponse
    {
        $xml = $this->responseParserCommon->parseResponse($rawResponse);
        $response = new CaptureResponse();
        $this->responseParserCommon->decorateWithStatus($xml, $response);

        if ($response->isApproved()) {
            $this->responseParserCommon->decorateWithTransactionDetails($xml, $response);
        } else {
            $this->responseParserCommon->decorateWithErrorDetails($xml, $response);
        }

        return $response;
    }
}
