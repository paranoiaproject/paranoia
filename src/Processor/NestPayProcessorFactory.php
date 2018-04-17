<?php
namespace Paranoia\Processor;

use Paranoia\Exception\InvalidArgumentException;
use Paranoia\Processor\NestPay\CancelResponseProcessor;
use Paranoia\Processor\NestPay\PostAuthorizationResponseProcessor;
use Paranoia\Processor\NestPay\PreAuthorizationResponseProcessor;
use Paranoia\Processor\NestPay\RefundResponseProcessor;
use Paranoia\Processor\NestPay\SaleResponseProcessor;
use Paranoia\TransactionType;

class NestPayProcessorFactory extends AbstractProcessorFactory
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
