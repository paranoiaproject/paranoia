<?php
namespace Paranoia\Acquirer\NestPay\RequestBuilder;

use Paranoia\Acquirer\NestPay\RequestBuilder\BaseRequestBuilder;
use Paranoia\Lib\Serializer\Serializer;
use Paranoia\Core\Model\Request;

class RefundRequestBuilder extends BaseRequestBuilder
{
    const TRANSACTION_TYPE = 'Credit';
    const ENVELOPE_NAME    = 'CC5Request';

    public function build(Request $request)
    {
        $data = array_merge(
            $this->buildBaseRequest(self::TRANSACTION_TYPE),
            [
                'OrderId'  => $request->getOrderId(),
            ]
        );

        if ($request->getAmount()) {
            $data = array_merge($data, [
                'Total'    => $this->amountFormatter->format($request->getAmount()),
                'Currency' => $this->currencyCodeFormatter->format($request->getCurrency()),
            ]);
        }

        $serializer = new Serializer(Serializer::XML);
        return $serializer->serialize($data, ['root_name' => self::ENVELOPE_NAME]);
    }
}
