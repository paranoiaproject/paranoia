<?php

namespace Paranoia\Core;

use Paranoia\Core\Exception\BadResponseException;
use Paranoia\Core\Exception\CommunicationError;
use Paranoia\Core\Exception\UnapprovedTransactionException;
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
use Paranoia\Core\Transaction\AuthorizationTransaction;
use Paranoia\Core\Transaction\CancelTransaction;
use Paranoia\Core\Transaction\CaptureTransaction;
use Paranoia\Core\Transaction\ChargeTransaction;
use Paranoia\Core\Transaction\RefundTransaction;

class ProviderContext
{
    /** @var AuthorizationTransaction */
    private $authorizationTransaction;

    /** @var CaptureTransaction */
    private $captureTransaction;

    /** @var ChargeTransaction */
    private $chargeTransaction;

    /** @var CancelTransaction */
    private $cancelTransaction;

    /** @var RefundTransaction */
    private $refundTransaction;

    /**
     * ProviderContext constructor.
     * @param AuthorizationTransaction $authorizationTransaction
     * @param CaptureTransaction $captureTransaction
     * @param ChargeTransaction $chargeTransaction
     * @param CancelTransaction $cancelTransaction
     * @param RefundTransaction $refundTransaction
     */
    public function __construct(
        AuthorizationTransaction $authorizationTransaction,
        CaptureTransaction $captureTransaction,
        ChargeTransaction $chargeTransaction,
        CancelTransaction $cancelTransaction,
        RefundTransaction $refundTransaction
    ) {
        $this->authorizationTransaction = $authorizationTransaction;
        $this->captureTransaction = $captureTransaction;
        $this->chargeTransaction = $chargeTransaction;
        $this->cancelTransaction = $cancelTransaction;
        $this->refundTransaction = $refundTransaction;
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
        return $this->authorizationTransaction->perform($request);
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
        return $this->captureTransaction->perform($request);
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
        return $this->chargeTransaction->perform($request);
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
        return $this->cancelTransaction->perform($request);
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
        return $this->refundTransaction->perform($request);
    }
}
