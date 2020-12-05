<?php
namespace Paranoia\Acquirer\Posnet\RequestBuilder;

use Paranoia\Core\Model\Request;
use Paranoia\Lib\Serializer\Serializer;

class AuthorizationRequestBuilder extends BaseRequestBuilder
{
    const TRANSACTION_TYPE = 'auth';
    const ENVELOPE_NAME    = 'posnetRequest';

    public function build(Request $request)
    {
        $data = array_merge(
            $this->buildBaseRequest($request),
            [
                self::TRANSACTION_TYPE => array_merge(
                    [
                        'amount' => $this->amountFormatter->format($request->getAmount()),
                        'currencyCode' => $this->currencyCodeFormatter->format($request->getCurrency()),
                        'orderID' => $this->orderIdFormatter->format($request->getOrderId())
                    ],
                    $this->buildCard($request->getCard())
                )
            ]
        );

        if ($request->getInstallment()) {
            $data[self::TRANSACTION_TYPE]['installment'] = $this->installmentFormatter->format(
                $request->getInstallment()
            );
        }

        $serializer = new Serializer(Serializer::XML);
        return $serializer->serialize($data, ['root_name' => self::ENVELOPE_NAME]);
    }
}
