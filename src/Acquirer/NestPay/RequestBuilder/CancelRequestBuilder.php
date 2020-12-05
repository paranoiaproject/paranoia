<?php
namespace Paranoia\Acquirer\NestPay\RequestBuilder;

use Paranoia\Acquirer\NestPay\NestPayConfiguration;
use Paranoia\Core\Model\Request\CancelRequest;
use Paranoia\Core\Model\Request\HttpRequest;
use Paranoia\Lib\XmlSerializer;

/**
 * Class CancelRequestBuilder
 * @package Paranoia\Acquirer\NestPay\RequestBuilder
 */
class CancelRequestBuilder
{
    private const TRANSACTION_TYPE = 'Void';

    /** @var NestPayConfiguration */
    private $configuration;

    /** @var RequestBuilderCommon */
    private $requestBuilderCommon;

    /** @var XmlSerializer */
    protected $serializer;

    /**
     * CancelRequestBuilder constructor.
     * @param NestPayConfiguration $configuration
     * @param RequestBuilderCommon $requestBuilderCommon
     * @param XmlSerializer $serializer
     */
    public function __construct(
        NestPayConfiguration $configuration,
        RequestBuilderCommon $requestBuilderCommon,
        XmlSerializer $serializer
    ) {
        $this->configuration = $configuration;
        $this->requestBuilderCommon = $requestBuilderCommon;
        $this->serializer = $serializer;
    }

    private function buildBody(CancelRequest $request)
    {
        $data = $this->requestBuilderCommon->buildBaseRequest(self::TRANSACTION_TYPE);

        if ($request->getOrderId() && !$request->getTransactionId()) {
            $data = array_merge($data, [
                'OrderId'  => $request->getOrderId()
            ]);
        }

        if ($request->getTransactionId()) {
            $data = array_merge($data, [
                'TransId' => $request->getTransactionId()
            ]);
        }

        $xmlData = $this->serializer->serialize($data, ['root_name' => RequestBuilderCommon::ENVELOPE_NAME]);
        return http_build_query([RequestBuilderCommon::FORM_FIELD => $xmlData]);
    }

    public function build(CancelRequest $request): HttpRequest
    {
        $headers = $this->requestBuilderCommon->buildHeaders();
        $body = $this->buildBody($request);

        return new HttpRequest($this->configuration->getApiUrl(), HttpRequest::HTTP_POST, $headers, $body);
    }
}
