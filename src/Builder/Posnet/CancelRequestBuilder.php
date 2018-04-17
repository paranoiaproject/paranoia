<?php
namespace Paranoia\Builder\Posnet;

use Paranoia\Common\Serializer\Serializer;
use Paranoia\Request;

class CancelRequestBuilder extends BaseRequestBuilder
{
    const TRANSACTION_TYPE = 'reverse';
    const ENVELOPE_NAME    = 'posnetRequest';

    const TEMPORARY_DEFAULT_TRANSACTION_TYPE ='sale'; //TODO: To be removed.

    public function build(Request $request)
    {
        //TODO: Normally, the bank can cancel any type of request
        // but we are going to be assumed we can just do 'cancel'
        // sale transactions for the first phase.

        $data = array_merge(
            $this->buildBaseRequest($request),
            [
                self::TRANSACTION_TYPE => [
                    'transaction' => self::TEMPORARY_DEFAULT_TRANSACTION_TYPE,
                    'hostLogKey' => $request->getTransactionId(),
                    //authCode just needed when VFT transaction performed
                    // For the other transaction types we can keep it as '000000'
                    'authCode' => '000000'
                ]
            ]
        );

        $serializer = new Serializer(Serializer::XML);
        return $serializer->serialize($data, ['root_name' => self::ENVELOPE_NAME]);
    }
}
