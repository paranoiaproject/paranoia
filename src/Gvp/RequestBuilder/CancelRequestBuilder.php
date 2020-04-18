<?php
namespace Paranoia\Gvp\RequestBuilder;

use Paranoia\Core\Request\CancelRequest;
use Paranoia\Core\RequestBuilder\CancelRequestBuilder as CoreCancelRequestBuilderAlias;
use Paranoia\Core\Serializer\Serializer;

class CancelRequestBuilder extends BaseRequestBuilder implements CoreCancelRequestBuilderAlias
{
    const TRANSACTION_TYPE = 'void';
    const ENVELOPE_NAME = 'GVPSRequest';
    const API_VERSION = 'v0.01';
    const CARD_HOLDER_PRESENT_CODE_NON_3D = 0;

    /**
     * @param CancelRequest $request
     * @return array
     */
    public function build(CancelRequest $request): array
    {
        $hash = $this->buildHash(
            [
                $request->getOrderId(),
                $this->configuration->getTerminalId(),
                0,
            ],
            $this->configuration->getRefundPassword()
        );

        $data = [
            'Version' => self::API_VERSION,
            'Mode' => $this->configuration->getMode(),
            'Terminal' => $this->buildTerminal($this->configuration->getRefundUsername(), $hash),
            'Order' => $this->buildOrder($request->getOrderId()),
            'Customer' => $this->buildCustomer(),
            'Transaction' => $this->buildTransaction($request->getTransactionId()),
        ];

        $serializer = new Serializer(Serializer::XML);
        $xml =  $serializer->serialize($data, ['root_name' => self::ENVELOPE_NAME]);
        return ['data' => $xml];
    }

    /**
     * @param string $transactionId
     * @return array
     */
    private function buildTransaction(?string $transactionId): array
    {
        $data = [
            'Type' => self::TRANSACTION_TYPE,
            'Amount' => 0,
            'CurrencyCode' => null,
            'CardholderPresentCode' => self::CARD_HOLDER_PRESENT_CODE_NON_3D,
            'MotoInd' => 'N',
            'OriginalRetrefNum' => $transactionId,
        ];

        return $data;
    }
}
