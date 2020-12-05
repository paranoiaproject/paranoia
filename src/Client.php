<?php
namespace Paranoia;

use Paranoia\Core\Acquirer\AcquirerAdapter;
use Paranoia\Core\Acquirer\Service\Factory\AbstractServiceFactory;
use Paranoia\Core\Model\Request\AuthorizationRequest;
use Paranoia\Core\Model\Request\CancelRequest;
use Paranoia\Core\Model\Request\CaptureRequest;
use Paranoia\Core\Model\Request\ChargeRequest;
use Paranoia\Core\Model\Request\RefundRequest;
use Paranoia\Core\Model\Response\AuthorizationResponse;
use Paranoia\Core\Model\Response\CancelResponse;
use Paranoia\Core\Model\Response\CaptureResponse;
use Paranoia\Core\Model\Response\ChargeResponse;
use Paranoia\Core\Model\Response\RefundResponse;

/**
 * Class Client
 * @package Paranoia
 */
class Client
{
    /** @var AcquirerAdapter */
    private $acquirerAdapter;

    /**
     * Client constructor.
     * @param AcquirerAdapter $acquirerAdapter
     */
    public function __construct(AcquirerAdapter $acquirerAdapter)
    {
        $this->acquirerAdapter = $acquirerAdapter;
    }

    /**
     * @param AuthorizationRequest $request
     * @return AuthorizationResponse
     */
    public function authorize(AuthorizationRequest $request): AuthorizationResponse
    {
        return $this->acquirerAdapter
            ->getServiceFactory(AbstractServiceFactory::AUTHORIZATION)
            ->create()
            ->process($request);
    }

    /**
     * @param CaptureRequest $request
     * @return CaptureResponse
     */
    public function capture(CaptureRequest $request): CaptureResponse
    {
        return $this->acquirerAdapter
            ->getServiceFactory(AbstractServiceFactory::CAPTURE)
            ->create()
            ->process($request);
    }

    /**
     * @param ChargeRequest $request
     * @return ChargeResponse
     */
    public function charge(ChargeRequest $request): ChargeResponse
    {
        return $this->acquirerAdapter
            ->getServiceFactory(AbstractServiceFactory::CHARGE)
            ->create()
            ->process($request);
    }

    /**
     * @param RefundRequest $request
     * @return RefundResponse
     */
    public function refund(RefundRequest $request): RefundResponse
    {
        return $this->acquirerAdapter
            ->getServiceFactory(AbstractServiceFactory::REFUND)
            ->create()
            ->process($request);
    }

    /**
     * @param CancelRequest $request
     * @return CancelResponse
     */
    public function cancel(CancelRequest $request): CancelResponse
    {
        return $this->acquirerAdapter
            ->getServiceFactory(AbstractServiceFactory::CANCEL)
            ->create()
            ->process($request);
    }
}