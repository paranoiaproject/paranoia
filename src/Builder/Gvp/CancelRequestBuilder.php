<?php
namespace Paranoia\Builder\Gvp;

use Paranoia\Common\Serializer\Serializer;
use Paranoia\Configuration\Gvp;
use Paranoia\Request;

class CancelRequestBuilder extends BaseRequestBuilder
{
    const TRANSACTION_TYPE = 'void';
    const ENVELOPE_NAME    = 'GVPSRequest';

    public function build(Request $request)
    {
        $data = $this->buildBaseRequest($request);
        $serializer = new Serializer(Serializer::XML);
        return $serializer->serialize($data, ['root_name' => self::ENVELOPE_NAME]);
    }

    protected function buildTransaction(Request $request)
    {
        return [
            'Type'                  => self::TRANSACTION_TYPE,
            'Amount'                => 1,
            'CurrencyCode'          => null,

            #TODO: Will be changed after 3D integration
            'CardholderPresentCode' => self::CARD_HOLDER_PRESENT_CODE_NON_3D,

            'MotoInd'               => 'N',
            'OriginalRetrefNum'     => $request->getTransactionId()
        ];
    }

    protected function getCredentialPair()
    {
        /** @var Gvp $configuration */
        $configuration = $this->configuration;
        return [$configuration->getRefundUsername(), $configuration->getRefundPassword()];
    }

    protected function buildHash(Request $request, $password)
    {
        /** @var Gvp $configuration */
        $configuration = $this->configuration;
        return strtoupper(
            sha1(
                sprintf(
                    '%s%s%s%s',
                    $request->getOrderId(),
                    $configuration->getTerminalId(),
                    1,
                    $this->generateSecurityHash($password)
                )
            )
        );
    }
}
