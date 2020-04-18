<?php
namespace Paranoia\Gvp\ResponseParser;

use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\InvalidArgumentException;
use Paranoia\Core\Exception\UnapprovedTransactionException;
use Paranoia\Core\Response\CancelResponse;
use Paranoia\Core\ResponseParser\CancelResponseParser as CoreCancelResponseParserAlias;
use Paranoia\Core\Transformer\XmlTransformer;
use Psr\Http\Message\ResponseInterface;

class CancelResponseParser implements CoreCancelResponseParserAlias
{
    /** @var XmlTransformer */
    private $transformer;

    /**
     * CancelResponseParser constructor.
     * @param XmlTransformer $transformer
     */
    public function __construct(XmlTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @param ResponseInterface $response
     * @return CancelResponse
     * @throws BadResponseException
     * @throws UnapprovedTransactionException
     */
    public function parse(ResponseInterface $response): CancelResponse
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
            return new CancelResponse(
                (string) $xml->Transaction->RetrefNum,
                (string) $xml->Transaction->AuthCode
            );
        } catch (InvalidArgumentException $exception) {
            throw new BadResponseException('Invalid provider response', 0, $exception);
        }
    }
}
