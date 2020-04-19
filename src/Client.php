<?php
namespace Paranoia;

use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\CommunicationError;
use Paranoia\Core\Exception\UnapprovedTransactionException;
use Paranoia\Core\ProviderContext;
use Paranoia\Core\ProviderContextFactory;
use Paranoia\Core\Request\AuthorizationRequest;
use Paranoia\Core\Request\CancelRequest;
use Paranoia\Core\Request\CaptureRequest;
use Paranoia\Core\Request\ChargeRequest;
use Paranoia\Core\Request\RefundRequest;
use Paranoia\Core\Response\AuthorizationResponse;
use Paranoia\Core\Response\CancelResponse;
use Paranoia\Core\Response\CaptureResponse;
use Paranoia\Core\Response\ChargeResponse;
use Paranoia\Core\Response\RefundResponse;

class Client
{
    /** @var ProviderContext */
    private $providerContext;

    /**
     * Client constructor.
     * @param ProviderContextFactory $providerContextFactory
     */
    public function __construct(ProviderContextFactory $providerContextFactory)
    {
        $this->providerContext = $providerContextFactory->createContext();
    }

    /**
     * @param AuthorizationRequest $request
     * @return AuthorizationResponse
     * @throws BadResponseException
     * @throws CommunicationError
     * @throws UnapprovedTransactionException
     */
    public function authorization(AuthorizationRequest $request): AuthorizationResponse
    {
        return $this->providerContext->authorization($request);
    }

    /**
     * @param CaptureRequest $request
     * @return CaptureResponse
     * @throws BadResponseException
     * @throws CommunicationError
     * @throws UnapprovedTransactionException
     */
    public function capture(CaptureRequest $request): CaptureResponse
    {
        return $this->providerContext->capture($request);
    }

    /**
     * @param ChargeRequest $request
     * @return ChargeResponse
     * @throws BadResponseException
     * @throws CommunicationError
     * @throws UnapprovedTransactionException
     */
    public function charge(ChargeRequest $request): ChargeResponse
    {
        return $this->providerContext->charge($request);
    }

    /**
     * @param CancelRequest $request
     * @return CancelResponse
     * @throws BadResponseException
     * @throws CommunicationError
     * @throws UnapprovedTransactionException
     */
    public function cancel(CancelRequest $request): CancelResponse
    {
        return $this->providerContext->cancel($request);
    }

    /**
     * @param RefundRequest $request
     * @return RefundResponse
     * @throws BadResponseException
     * @throws CommunicationError
     * @throws UnapprovedTransactionException
     */
    public function refund(RefundRequest $request): RefundResponse
    {
        return $this->providerContext->refund($request);
    }
}
