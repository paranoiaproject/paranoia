<?php
namespace Paranoia\Gvp\ResponseParser;

use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\InvalidArgumentException;
use Paranoia\Core\Exception\UnapprovedTransactionException;
use Paranoia\Core\Response\CaptureResponse;
use Paranoia\Core\ResponseParser\CaptureResponseParser as CoreCaptureResponseParserAlias;
use Paranoia\Core\Transformer\XmlTransformer;
use Psr\Http\Message\ResponseInterface;

class CaptureResponseParser implements CoreCaptureResponseParserAlias
{
    /** @var XmlTransformer */
    private $transformer;

    /**
     * CaptureResponseParser constructor.
     * @param XmlTransformer $transformer
     */
    public function __construct(XmlTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @param ResponseInterface $response
     * @return CaptureResponse
     * @throws BadResponseException
     * @throws UnapprovedTransactionException
     */
    public function parse(ResponseInterface $response): CaptureResponse
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
            return new CaptureResponse(
                (string) $xml->Transaction->RetrefNum,
                (string) $xml->Transaction->AuthCode
            );
        } catch (InvalidArgumentException $exception) {
            throw new BadResponseException('Invalid provider response', 0, $exception);
        }
    }
}
