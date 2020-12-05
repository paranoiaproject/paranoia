<?php
namespace Paranoia\Acquirer\NestPay\RequestBuilder;

use Paranoia\Acquirer\NestPay\RequestBuilder\BaseRequestBuilder;
use Paranoia\Lib\Serializer\Serializer;
use Paranoia\Core\Model\Request;

class SaleRequestBuilder extends BaseRequestBuilder
{
    const TRANSACTION_TYPE = 'Auth';
    const ENVELOPE_NAME    = 'CC5Request';

    public function build(Request $request)
    {
        $data = array_merge(
            $this->buildBaseRequest(self::TRANSACTION_TYPE),
            [
                'OrderId'  => $request->getOrderId(),
                'Total'    => $this->amountFormatter->format($request->getAmount()),
                'Currency' => $this->currencyCodeFormatter->format($request->getCurrency()),
            ],
            $this->buildCard($request->getResource())
        );

        if ($request->getInstallment()) {
            $data['Taksit'] = $this->installmentFormatter->format($request->getInstallment());
        }

        $serializer = new Serializer(Serializer::XML);
        return $serializer->serialize($data, ['root_name' => self::ENVELOPE_NAME]);
    }
}
