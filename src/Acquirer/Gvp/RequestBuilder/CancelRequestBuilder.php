<?php
namespace Paranoia\Acquirer\Gvp\RequestBuilder;

use Paranoia\Acquirer\Gvp\GvpConfiguration;
use Paranoia\Core\Model\Request;
use Paranoia\Lib\Serializer\Serializer;

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
        /** @var GvpConfiguration $configuration */
        $configuration = $this->configuration;
        return [$configuration->getRefundUsername(), $configuration->getRefundPassword()];
    }

    protected function buildHash(Request $request, $password)
    {
        /** @var GvpConfiguration $configuration */
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
