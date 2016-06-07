<?php
namespace Paranoia\Builder\NestPay;

use Paranoia\Helper\Serializer\Serializer;
use Paranoia\Helper\Serializer\Serializer\Xml;
use Paranoia\Transfer\Request\CancelRequest;
use Paranoia\Transfer\Request\RequestInterface;


class CancelRequestBuilder extends AbstractNestPayBuilder
{
    const TRANSACTION_TYPE = 'Void';

    /**
     * @param CancelRequest $request
     */
    private function prepareRequest(CancelRequest $request)
    {
        $data = $this->prepareCommonParameters();

        $data = array_merge(
            $data,
            array(
                'Type'     => self::TRANSACTION_TYPE,
                'OrderId'  => $request->getOrderId(),
            )
        );

        if($request->getTransactionId()) {
            $data['TransId'] = $request->getTransactionId();
        }
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