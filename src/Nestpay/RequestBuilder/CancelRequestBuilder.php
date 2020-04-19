<?php
namespace Paranoia\Nestpay\RequestBuilder;

use Paranoia\Configuration\NestpayConfiguration;
use Paranoia\Core\Request\CancelRequest;
use Paranoia\Core\RequestBuilder\CancelRequestBuilder as CoreCancelRequestBuilderAlias;
use Paranoia\Core\Serializer\Serializer;

class CancelRequestBuilder implements CoreCancelRequestBuilderAlias
{
    const TRANSACTION_TYPE = 'Void';
    const ENVELOPE_NAME = 'CC5Request';
    const FORM_FIELD = 'DATA';

    /** @var NestpayConfiguration */
    protected $configuration;

    /**
     * CancelRequestBuilder constructor.
     * @param NestpayConfiguration $configuration
     */
    public function __construct(NestpayConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function build(CancelRequest $request): array
    {
        //TODO: validation require
        $data = [
            'Name' => $this->configuration->getUsername(),
            'ClientId' => $this->configuration->getClientId(),
            'Type' => self::TRANSACTION_TYPE,
        ];

        if ($request->getOrderId()) {
            $data['OrderId'] = $request->getOrderId();
        } else if ($request->getTransactionId()) {
            $data['TransId'] = $request->getTransactionId();
        }

        $serializer = new Serializer(Serializer::XML);
        $xml =  $serializer->serialize($data, ['root_name' => self::ENVELOPE_NAME]);
        return [self::FORM_FIELD => $xml];
    }
}
