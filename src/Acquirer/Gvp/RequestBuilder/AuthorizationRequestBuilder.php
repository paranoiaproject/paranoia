<?php
namespace Paranoia\Acquirer\Gvp\RequestBuilder;

use Paranoia\Acquirer\Gvp\GvpConfiguration;
use Paranoia\Core\Model\Request;
use Paranoia\Lib\Serializer\Serializer;

class AuthorizationRequestBuilder extends BaseRequestBuilder
{
    const TRANSACTION_TYPE = 'preauth';
    const ENVELOPE_NAME    = 'GVPSRequest';

    public function build(Request $request)
    {
        $data = array_merge(
            $this->buildBaseRequest($request),
            ['Card' => $this->buildCard($request->getResource())]
        );

        $serializer = new Serializer(Serializer::XML);
        return $serializer->serialize($data, ['root_name' => self::ENVELOPE_NAME]);
    }

    protected function buildTransaction(Request $request)
    {
        return [
            'Type'                  => self::TRANSACTION_TYPE,
            'Amount'                => $this->amountFormatter->format($request->getAmount()),
            'CurrencyCode'          => $this->currencyCodeFormatter->format($request->getCurrency()),

            #TODO: Will be changed after 3D integration
            'CardholderPresentCode' => self::CARD_HOLDER_PRESENT_CODE_NON_3D,

            'MotoInd'               => 'N',
            'OriginalRetrefNum'     => null,
        ];
    }

    protected function getCredentialPair()
    {
        /** @var GvpConfiguration $configuration */
        $configuration = $this->configuration;
        return [$configuration->getAuthorizationUsername(), $configuration->getAuthorizationPassword()];
    }

    protected function buildHash(Request $request, $password)
    {
        /** @var GvpConfiguration $configuration */
        $configuration = $this->configuration;
        return strtoupper(
            sha1(
                sprintf(
                    '%s%s%s%s%s',
                    $request->getOrderId(),
                    $configuration->getTerminalId(),
                    $request->getResource()->getNumber(),
                    $this->amountFormatter->format($request->getAmount()),
                    $this->generateSecurityHash($password)
                )
            )
        );
    }
}
