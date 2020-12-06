<?php
namespace Paranoia\Acquirer\Gvp\RequestBuilder;

use Paranoia\Acquirer\Gvp\GvpConfiguration;
use Paranoia\Core\Model\Request\CancelRequest;
use Paranoia\Core\Model\Request\HttpRequest;
use Paranoia\Lib\XmlSerializer;

/**
 * Class CancelRequestBuilder
 * @package Paranoia\Acquirer\Gvp\RequestBuilder
 */
class CancelRequestBuilder
{
    const TRANSACTION_TYPE = 'void';

    /** @var GvpConfiguration */
    private $configuration;

    /** @var RequestBuilderCommon */
    private $requestBuilderCommon;

    /** @var XmlSerializer */
    protected $serializer;

    /**
     * CancelRequestBuilder constructor.
     * @param GvpConfiguration $configuration
     * @param RequestBuilderCommon $requestBuilderCommon
     * @param XmlSerializer $serializer
     */
    public function __construct(
        GvpConfiguration $configuration,
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
        $hash = $this->requestBuilderCommon->buildHAshWithoutCard(
            $request->getOrderId(),
            '1',
            $this->configuration->getAuthorizationPassword()
        );

        $terminal = $this->requestBuilderCommon->buildTerminal($this->configuration->getAuthorizationUsername(), $hash);
        $order = $this->requestBuilderCommon->buildOrder($request->getOrderId());

        $transaction = $this->requestBuilderCommon->buildTransaction(
            self::TRANSACTION_TYPE,
            '1',
            null
        );

        $baseRequest = $this->requestBuilderCommon->buildBaseRequest($terminal, $order, $transaction);

        $xmlData = $this->serializer->serialize($baseRequest, ['root_name' => RequestBuilderCommon::ENVELOPE_NAME]);
        return http_build_query([RequestBuilderCommon::FORM_FIELD => $xmlData]);
    }
}
