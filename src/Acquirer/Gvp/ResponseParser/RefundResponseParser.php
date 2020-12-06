<?php
namespace Paranoia\Acquirer\Gvp\ResponseParser;

use Paranoia\Core\Model\Response\RefundResponse;

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
     * @throws \Paranoia\Core\Exception\BadResponseException
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
