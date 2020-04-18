<?php
namespace Paranoia\Nestpay\ResponseParser;

use Paranoia\Core\Exception\InvalidArgumentException;
use Paranoia\Core\Exception\BadResponseException;
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
     * AuthorizationResponseParser constructor.
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
            if ( strtolower((string) $xml->Response) != 'approved') {
                throw new UnapprovedTransactionException(
                    (string) $xml->ErrMsg,
                    (string) $xml->Extra->ERRORCODE,
                    (string) $xml->Extra->HOSTMSG
                );
            }
            return new CaptureResponse((string) $xml->TransId, (string) $xml->AuthCode);
        } catch (InvalidArgumentException $exception) {
            throw new BadResponseException('Invalid provider response', 0, $exception);
        }
    }
}
