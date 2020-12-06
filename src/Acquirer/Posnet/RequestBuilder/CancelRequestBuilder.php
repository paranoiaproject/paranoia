<?php
namespace Paranoia\Acquirer\Posnet\RequestBuilder;

use Paranoia\Acquirer\Posnet\PosnetConfiguration;
use Paranoia\Core\Model\Request\CancelRequest;
use Paranoia\Core\Model\Request\HttpRequest;
use Paranoia\Lib\XmlSerializer;

/**
 * Class CancelRequestBuilder
 * @package Paranoia\Acquirer\Posnet\RequestBuilder
 */
class CancelRequestBuilder
{
    private const TRANSACTION_TYPE = 'reverse';
    private const DEFAULT_VOID_TRANSACTION_TYPE ='sale'; //TODO: To be removed.

    /** @var PosnetConfiguration */
    private $configuration;

    /** @var RequestBuilderCommon */
    private $requestBuilderCommon;

    /** @var XmlSerializer */
    private $serializer;

    /**
     * CancelRequestBuilder constructor.
     * @param PosnetConfiguration $configuration
     * @param RequestBuilderCommon $requestBuilderCommon
     * @param XmlSerializer $serializer
     */
    public function __construct(
        PosnetConfiguration $configuration,
        RequestBuilderCommon $requestBuilderCommon,
        XmlSerializer $serializer
    ) {
        $this->configuration = $configuration;
        $this->requestBuilderCommon = $requestBuilderCommon;
        $this->serializer = $serializer;
    }

    /**
     * @param CancelRequest $request
     * @return HttpRequest
     */
    public function build(CancelRequest $request): HttpRequest
    {
        $headers = $this->requestBuilderCommon->buildHeaders();
        $body = $this->buildBody($request);

        return new HttpRequest($this->configuration->getApiUrl(), HttpRequest::HTTP_POST, $headers, $body);
    }

    /**
     * @param CancelRequest $request
     * @return string
     */
    private function buildBody(CancelRequest $request): string
    {
        //TODO: Normally, the bank can cancel any type of request
        // but we are going to be assumed we can just do 'cancel'
        // sale transactions for the first phase.

        $data = array_merge($this->requestBuilderCommon->buildBaseRequest(), $this->buildCancel($request));

        $xmlData = $this->serializer->serialize($data, ['root_name' => RequestBuilderCommon::ENVELOPE_NAME]);
        return http_build_query([RequestBuilderCommon::FORM_FIELD => $xmlData]);
    }

    /**
     * @param CancelRequest $request
     * @return array
     */
    private function buildCancel(CancelRequest $request): array
    {
        return [
            self::TRANSACTION_TYPE => [
                'transaction' => self::DEFAULT_VOID_TRANSACTION_TYPE,
                'hostLogKey' => $request->getTransactionId(),
                //authCode just needed when VFT transaction performed
                // For the other transaction types we can keep it as '000000'
                'authCode' => '000000'
            ]
        ];
    }
}
