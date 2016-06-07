<?php
namespace Paranoia\Builder\NestPay;

use Paranoia\Helper\Serializer\Serializer;
use Paranoia\Helper\Serializer\Serializer\Xml;
use Paranoia\Transfer\Request\PostAuthorizationRequest;
use Paranoia\Transfer\Request\RequestInterface;

class PostAuthorizationRequestBuilder extends AbstractNestPayBuilder
{
    const TRANSACTION_TYPE = 'PostAuth';

    /**
     * @param PostAuthorizationRequest $request
     * @return array
     */
    private function prepareRequest(PostAuthorizationRequest $request)
    {
        $data = $this->prepareCommonParameters();

        return array_merge(
            $data,
            array(
                'Type'     => self::TRANSACTION_TYPE,
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