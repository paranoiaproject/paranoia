<?php
namespace Paranoia\Gvp\ResponseParser;

use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\InvalidArgumentException;
use Paranoia\Core\Exception\UnapprovedTransactionException;
use Paranoia\Core\Response\AuthorizationResponse;
use Paranoia\Core\ResponseParser\AuthorizationResponseParser as CoreAuthorizationResponseParserAlias;
use Paranoia\Core\Transformer\XmlTransformer;
use Psr\Http\Message\ResponseInterface;

class AuthorizationResponseParser implements CoreAuthorizationResponseParserAlias
{
    /** @var XmlTransformer */
    private $transformer;

    /**
     * AuthorizationResponseParser constructor.
     * @param XmlTransformer $transformer
     */
    public function __construct(XmlTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @param ResponseInterface $response
     * @return AuthorizationResponse
     * @throws BadResponseException
     * @throws UnapprovedTransactionException
     */
    public function parse(ResponseInterface $response): AuthorizationResponse
    {
        try {
            $xml = $this->transformer->transform($response->getBody());
            if ( '00' != (string) $xml->Transaction->Response->Code) {
                throw new UnapprovedTransactionException(
                    (string) $xml->Transaction->Response->ErrorMsg,
                    (string) $xml->Transaction->Response->ReasonCode,
                    (string) $xml->Transaction->Response->SysErrMsg
                );
            }
            return new AuthorizationResponse(
                (string) $xml->Transaction->RetrefNum,
                (string) $xml->Transaction->AuthCode
            );
        } catch (InvalidArgumentException $exception) {
            throw new BadResponseException('Invalid provider response', 0, $exception);
        }
    }
}
