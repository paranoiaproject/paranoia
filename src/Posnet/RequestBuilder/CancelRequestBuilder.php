<?php
namespace Paranoia\Posnet\RequestBuilder;

use Paranoia\Configuration\PosnetConfiguration;
use Paranoia\Core\Request\CancelRequest;
use Paranoia\Core\RequestBuilder\CancelRequestBuilder as CoreCancelRequestBuilderAlias;
use Paranoia\Core\Serializer\Serializer;

class CancelRequestBuilder implements CoreCancelRequestBuilderAlias
{
    const TRANSACTION_TYPE = 'reverse';
    const ENVELOPE_NAME    = 'posnetRequest';
    const FORM_FIELD = 'xmldata';
    // Posnet expect the transaction type besides the identifier
    // to cancel the transaction. However, I use `charge/sale` transaction
    // type as default transaction type in order to make the implementation
    //// compatible with the other ones.
    const TEMPORARY_DEFAULT_TRANSACTION_TYPE ='sale'; //TODO: To be removed.

    /** @var PosnetConfiguration */
    protected $configuration;

    /**
     * CancelRequestBuilder constructor.
     * @param PosnetConfiguration $configuration
     */
    public function __construct(PosnetConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function build(CancelRequest $request): array
    {
        $data = [
            'mid' => $this->configuration->getMerchantId(),
            'tid' => $this->configuration->getTerminalId(),
            'username' => $this->configuration->getUsername(),
            'password' => $this->configuration->getPassword(),
            self::TRANSACTION_TYPE => [
                'transaction' => self::TEMPORARY_DEFAULT_TRANSACTION_TYPE,
                'hostLogKey' => $request->getTransactionId(),
                //authCode just needed when VFT transaction performed
                // For the other transaction types we can keep it as '000000'
                'authCode' => '000000'
            ]
        ];
        $serializer = new Serializer(Serializer::XML);
        $xml =  $serializer->serialize($data, ['root_name' => self::ENVELOPE_NAME]);
        return [self::FORM_FIELD => $xml];
    }
}
