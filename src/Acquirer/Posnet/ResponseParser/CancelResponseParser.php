<?php
namespace Paranoia\Acquirer\Posnet\ResponseParser;

use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Model\Response\CancelResponse;

/**
 * Class CancelResponseParser
 * @package Paranoia\Acquirer\Posnet\ResponseParser
 */
class CancelResponseParser
{
    /** @var ResponseParserCommon */
    private $responseParserCommon;

    /**
     * CancelResponseParser constructor.
     * @param ResponseParserCommon $responseParserCommon
     */
    public function __construct(ResponseParserCommon $responseParserCommon)
    {
        $this->responseParserCommon = $responseParserCommon;
    }

    /**
     * @param string $rawResponse
     * @return CancelResponse
     * @throws BadResponseException
     */
    public function parse(string $rawResponse): CancelResponse
    {
        $xml = $this->responseParserCommon->parseResponse($rawResponse);
        $response = new CancelResponse();
        $this->responseParserCommon->decorateWithStatus($xml, $response);

        if (!$response->isApproved()) {
            $this->responseParserCommon->decorateWithErrorDetails($xml, $response);
        }

        return $response;
    }
}
