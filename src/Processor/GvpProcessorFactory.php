<?php
namespace Paranoia\Processor;

use Paranoia\Exception\InvalidArgumentException;
use Paranoia\Processor\Gvp\CancelResponseProcessor;
use Paranoia\Processor\Gvp\PostAuthorizationResponseProcessor;
use Paranoia\Processor\Gvp\PreAuthorizationResponseProcessor;
use Paranoia\Processor\Gvp\RefundResponseProcessor;
use Paranoia\Processor\Gvp\SaleResponseProcessor;
use Paranoia\TransactionType;

class GvpProcessorFactory extends AbstractProcessorFactory
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
