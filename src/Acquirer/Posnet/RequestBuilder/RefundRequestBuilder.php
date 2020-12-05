<?php
namespace Paranoia\Acquirer\Posnet\RequestBuilder;

use Paranoia\Acquirer\Posnet\RequestBuilder\BaseRequestBuilder;
use Paranoia\Lib\Serializer\Serializer;
use Paranoia\Core\Model\Request;

class RefundRequestBuilder extends BaseRequestBuilder
{
    const TRANSACTION_TYPE = 'return';
    const ENVELOPE_NAME    = 'posnetRequest';

    public function build(Request $request)
    {
        $data = array_merge(
            $this->buildBaseRequest($request),
            [
                self::TRANSACTION_TYPE => [
                    'amount' => $this->amountFormatter->format($request->getAmount()),
                    'currencyCode' => $this->currencyCodeFormatter->format($request->getCurrency()),
                    'hostLogKey' => $request->getTransactionId()
                ]
            ]
        );

        $serializer = new Serializer(Serializer::XML);
        return $serializer->serialize($data, ['root_name' => self::ENVELOPE_NAME]);
    }
}
