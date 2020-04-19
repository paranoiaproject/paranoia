<?php
namespace Paranoia\Posnet\RequestBuilder;

use Paranoia\Configuration\PosnetConfiguration;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Request\CaptureRequest;
use Paranoia\Core\RequestBuilder\CaptureRequestBuilder as CoreCaptureRequestBuilderAlias;
use Paranoia\Core\Serializer\Serializer;
use Paranoia\Posnet\Formatter\CustomCurrencyCodeFormatter;

class CaptureRequestBuilder implements CoreCaptureRequestBuilderAlias
{
    const TRANSACTION_TYPE = 'capt';
    const ENVELOPE_NAME    = 'posnetRequest';
    const FORM_FIELD = 'xmldata';

    /** @var PosnetConfiguration */
    protected $configuration;

    /** @var MoneyFormatter */
    protected $amountFormatter;

    /** @var CustomCurrencyCodeFormatter */
    protected $currencyFormatter;

    /**
     * CaptureRequestBuilder constructor.
     * @param PosnetConfiguration $configuration
     * @param MoneyFormatter $amountFormatter
     * @param CustomCurrencyCodeFormatter $currencyFormatter
     */
    public function __construct(
        PosnetConfiguration $configuration,
        MoneyFormatter $amountFormatter,
        CustomCurrencyCodeFormatter $currencyFormatter
    ) {
        $this->configuration = $configuration;
        $this->amountFormatter = $amountFormatter;
        $this->currencyFormatter = $currencyFormatter;
    }

    public function build(CaptureRequest $request): array
    {
        $data = [
            'mid' => $this->configuration->getMerchantId(),
            'tid' => $this->configuration->getTerminalId(),
            'username' => $this->configuration->getUsername(),
            'password' => $this->configuration->getPassword(),
            self::TRANSACTION_TYPE => [
                'amount' => $this->amountFormatter->format($request->getAmount()),
                'currencyCode' => $this->currencyFormatter->format($request->getCurrency()),
                'hostLogKey' => $request->getTransactionRef(),
            ]
        ];
        $serializer = new Serializer(Serializer::XML);
        $xml =  $serializer->serialize($data, ['root_name' => self::ENVELOPE_NAME]);
        return [self::FORM_FIELD => $xml];
    }
}
