<?php
namespace Paranoia\Builder\NestPay;

use Paranoia\Common\Serializer\Serializer;
use Paranoia\Request;

class PreAuthorizationRequestBuilder extends BaseRequestBuilder
{
    const TRANSACTION_TYPE = 'PreAuth';
    const ENVELOPE_NAME    = 'CC5Request';

    public function build(Request $request)
    {
        $data = array_merge(
            $this->buildBaseRequest(self::TRANSACTION_TYPE),
            [
                'OrderId'  => $request->getOrderId(),
                'Total'    => $this->amountFormatter->format($request->getAmount()),
                'Currency' => $this->currencyCodeFormatter->format($request->getCurrency()),
                'Number'   => $request->getCardNumber(),
                'Cvv2Val'  => $request->getSecurityCode(),
                'Expires'  => $this->expireDateFormatter->format(
                    [
                        $request->getExpireMonth(),
                        $request->getExpireYear()
                    ]
                ),
            ]
        );

        $serializer = new Serializer(Serializer::XML);
        return $serializer->serialize($data, ['root_name' => self::ENVELOPE_NAME]);
    }
}
