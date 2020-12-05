<?php
namespace Paranoia\Acquirer\NestPay;

use Paranoia\Acquirer\AbstractResponseParser;
use Paranoia\Acquirer\AbstractResponseParserFactory;
use Paranoia\Acquirer\NestPay\ResponseParser\AuthorizationResponseParser;
use Paranoia\Acquirer\NestPay\ResponseParser\CancelResponseParser;
use Paranoia\Acquirer\NestPay\ResponseParser\CaptureResponseParser;
use Paranoia\Acquirer\NestPay\ResponseParser\ChargeResponseParser;
use Paranoia\Acquirer\NestPay\ResponseParser\RefundResponseParser;
use Paranoia\Core\Constant\TransactionType;
use Paranoia\Core\Exception\InvalidArgumentException;

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
                return new ChargeResponseParser($this->configuration);
            case TransactionType::REFUND:
                return new RefundResponseParser($this->configuration);
            case TransactionType::CANCEL:
                return new CancelResponseParser($this->configuration);
            case TransactionType::PRE_AUTHORIZATION:
                return new AuthorizationResponseParser($this->configuration);
            case TransactionType::POST_AUTHORIZATION:
                return new CaptureResponseParser($this->configuration);
            default:
                throw new InvalidArgumentException('Bad transaction type: ' . $transactionType);
        }
    }
}
