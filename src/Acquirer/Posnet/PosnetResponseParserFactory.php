<?php
namespace Paranoia\Acquirer\Posnet;

use Paranoia\Acquirer\AbstractResponseParser;
use Paranoia\Acquirer\AbstractResponseParserFactory;
use Paranoia\Acquirer\Posnet\ResponseParser\CancelResponseParser;
use Paranoia\Acquirer\Posnet\ResponseParser\ChargeResponseParser;
use Paranoia\Acquirer\Posnet\ResponseParser\CaptureResponseParser;
use Paranoia\Acquirer\Posnet\ResponseParser\AuthorizationResponseParser;
use Paranoia\Acquirer\Posnet\ResponseParser\RefundResponseParser;
use Paranoia\Core\Constant\TransactionType;
use Paranoia\Core\Exception\InvalidArgumentException;

class PosnetResponseParserFactory extends AbstractResponseParserFactory
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
