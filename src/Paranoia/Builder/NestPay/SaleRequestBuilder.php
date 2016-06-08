<?php
namespace Paranoia\Builder\NestPay;

use Paranoia\Helper\Formatter\Formatter\MoneyValue;
use Paranoia\Helper\Formatter\Formatter\NoneZeroPositiveIntegerValue;
use Paranoia\Helper\Formatter\Formatter\SlashSeparatedDate;
use Paranoia\Helper\Serializer\Serializer;
use Paranoia\Helper\Serializer\Serializer\Xml;
use Paranoia\Transfer\Request\RequestInterface;
use Paranoia\Transfer\Request\SaleRequest;


class SaleRequestBuilder extends AbstractNestPayBuilder
{
    const TRANSACTION_TYPE = 'Auth';


    /**
     * @param SaleRequest $request
     * @return array
     */
    private function prepareRequest(SaleRequest $request)
    {
        /** @var $resource \Paranoia\Transfer\Request\Resource\PaymentCard */
        $resource = $request->getResource();

        $data = $this->prepareCommonParameters();

        return array_merge(
            $data,
            array(
                'Type'     => self::TRANSACTION_TYPE,
                'Total'    => MoneyValue::format($request->getAmount()),
                'Currency' => $request->getCurrency()->getNumericCode(),
                'Taksit'   => NoneZeroPositiveIntegerValue::format($resource->getInstallment()),
                'Number'   => $resource->getNumber(),
                'Cvv2Val'  => $resource->getSecurityCode(),
                'Expires'  => SlashSeparatedDate::format($resource->getExpireMonth(), $resource->getExpireYear()),
                'OrderId'  => $request->getOrderId()
        ));
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
