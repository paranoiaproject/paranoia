<?php
namespace Paranoia\Acquirer\Posnet\ResponseParser;

use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Model\Response\AuthorizationResponse;

/**
 * Class AuthorizationResponseParser
 * @package Paranoia\Acquirer\Posnet\ResponseParser
 */
class AuthorizationResponseParser
{
    /** @var ResponseParserCommon */
    private $responseParserCommon;

    /**
     * AuthorizationResponseParser constructor.
     * @param ResponseParserCommon $responseParserCommon
     */
    public function __construct(ResponseParserCommon $responseParserCommon)
    {
        $this->responseParserCommon = $responseParserCommon;
    }

    /**
     * @param string $rawResponse
     * @return AuthorizationResponse
     * @throws BadResponseException
     */
    public function parse(string $rawResponse): AuthorizationResponse
    {
        $xml = $this->responseParserCommon->parseResponse($rawResponse);
        $response = new AuthorizationResponse();
        $this->responseParserCommon->decorateWithStatus($xml, $response);

        if ($response->isApproved()) {
            $this->responseParserCommon->decorateWithTransactionDetails($xml, $response);
        } else {
            $this->responseParserCommon->decorateWithErrorDetails($xml, $response);
        }

        return $response;
    }
}
