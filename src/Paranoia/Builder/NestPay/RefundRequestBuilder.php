<?php
namespace Paranoia\Builder\NestPay;

use Paranoia\Formatter\Formatter\MoneyValue;
use Paranoia\Helper\Serializer\Serializer;
use Paranoia\Helper\Serializer\Serializer\Xml;
use Paranoia\Transfer\Request\RefundRequest;
use Paranoia\Transfer\Request\RequestInterface;

class RefundRequestBuilder extends AbstractNestPayBuilder
{
    const TRANSACTION_TYPE = 'Credit';

    /**
     * @param RefundRequest $request
     * @return array
     */
    private function prepareRequest(RefundRequest $request)
    {
        $data = $this->prepareCommonParameters();

        return array_merge(
            $data,
            array(
                'Type'     => self::TRANSACTION_TYPE,
                'Total'    => MoneyValue::format($request->getAmount()),
                'Currency' => $request->getCurrency()->getNumericCode(),
                'OrderId'  => $request->getOrderId(),
            )
        );
    }

    /**
     * @param RequestInterface $request
     * @return mixed
     */
    public function build(RequestInterface $request)
    {
        $data = $this->prepareRequest($request);
        $xml = Serializer::serialize(new Xml(), $data, array('root_name' => 'CC5Request'));
        return array('DATA' => $xml);
    }
}