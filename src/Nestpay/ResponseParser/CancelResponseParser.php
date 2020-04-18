<?php
namespace Paranoia\Nestpay\ResponseParser;

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
     * AuthorizationResponseParser constructor.
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
            if ( strtolower((string) $xml->Response) != 'approved') {
                throw new UnapprovedTransactionException(
                    (string) $xml->ErrMsg,
                    (string) $xml->Extra->ERRORCODE,
                    (string) $xml->Extra->HOSTMSG
                );
            }
            return new CancelResponse((string) $xml->TransId, (string) $xml->AuthCode);
        } catch (InvalidArgumentException $exception) {
            throw new BadResponseException('Invalid provider response', 0, $exception);
        }
    }
}
