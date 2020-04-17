<?php
namespace Paranoia\Builder\NestPay;

use Paranoia\Core\Serializer\Serializer;
use Paranoia\Request\Request;

class PostAuthorizationRequestBuilder extends BaseRequestBuilder
{
    const TRANSACTION_TYPE = 'PostAuth';
    const ENVELOPE_NAME = 'CC5Request';

    public function build(Request $request)
    {
        $data = array_merge($this->buildBaseRequest(self::TRANSACTION_TYPE), [
            'OrderId'  => $request->getOrderId()
        ]);

        $serializer = new Serializer(Serializer::XML);
        return $serializer->serialize($data, ['root_name' => self::ENVELOPE_NAME]);
    }
}
