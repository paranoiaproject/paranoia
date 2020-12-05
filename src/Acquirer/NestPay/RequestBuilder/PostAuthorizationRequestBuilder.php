<?php
namespace Paranoia\Acquirer\NestPay\RequestBuilder;

use Paranoia\Core\Model\Request;
use Paranoia\Lib\Serializer\Serializer;

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
