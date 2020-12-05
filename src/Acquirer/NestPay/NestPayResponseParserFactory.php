<?php
namespace Paranoia\Acquirer\NestPay;

use Paranoia\Acquirer\AbstractResponseParser;
use Paranoia\Acquirer\AbstractResponseParserFactory;
use Paranoia\Core\Exception\InvalidArgumentException;
use Paranoia\Acquirer\NestPay\ResponseParser\CancelResponseParser;
use Paranoia\Acquirer\NestPay\ResponseParser\PostAuthorizationResponseParser;
use Paranoia\Acquirer\NestPay\ResponseParser\PreAuthorizationResponseParser;
use Paranoia\Acquirer\NestPay\ResponseParser\RefundResponseParser;
use Paranoia\Acquirer\NestPay\ResponseParser\SaleResponseParser;
use Paranoia\Core\Constant\TransactionType;

class NestPayResponseParserFactory extends AbstractResponseParserFactory
{
    /**
     * @param string $transactionType
     * @return AbstractResponseParser
     */
    public function createProcessor($transactionType)
    {
        switch ($transactionType) {
            case TransactionType::SALE:
                return new SaleResponseParser($this->configuration);
            case TransactionType::REFUND:
                return new RefundResponseParser($this->configuration);
            case TransactionType::CANCEL:
                return new CancelResponseParser($this->configuration);
            case TransactionType::PRE_AUTHORIZATION:
                return new PreAuthorizationResponseParser($this->configuration);
            case TransactionType::POST_AUTHORIZATION:
                return new PostAuthorizationResponseParser($this->configuration);
            default:
                throw new InvalidArgumentException('Bad transaction type: ' . $transactionType);
        }
    }
}
