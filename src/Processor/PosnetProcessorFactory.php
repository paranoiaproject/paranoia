<?php
namespace Paranoia\Processor;

use Paranoia\Exception\InvalidArgumentException;
use Paranoia\Processor\Posnet\CancelResponseProcessor;
use Paranoia\Processor\Posnet\PostAuthorizationResponseProcessor;
use Paranoia\Processor\Posnet\PreAuthorizationResponseProcessor;
use Paranoia\Processor\Posnet\RefundResponseProcessor;
use Paranoia\Processor\Posnet\SaleResponseProcessor;
use Paranoia\TransactionType;

class PosnetProcessorFactory extends AbstractProcessorFactory
{
    /**
     * @param string $transactionType
     * @return AbstractResponseProcessor
     */
    public function createProcessor($transactionType)
    {
        switch ($transactionType) {
            case TransactionType::SALE:
                return new SaleResponseProcessor($this->configuration);
            case TransactionType::REFUND:
                return new RefundResponseProcessor($this->configuration);
            case TransactionType::CANCEL:
                return new CancelResponseProcessor($this->configuration);
            case TransactionType::PRE_AUTHORIZATION:
                return new PreAuthorizationResponseProcessor($this->configuration);
            case TransactionType::POST_AUTHORIZATION:
                return new PostAuthorizationResponseProcessor($this->configuration);
            default:
                throw new InvalidArgumentException('Bad transaction type: ' . $transactionType);
        }
    }
}
