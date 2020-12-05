<?php
namespace Paranoia\Acquirer\Gvp;

use Paranoia\Acquirer\AbstractResponseParser;
use Paranoia\Acquirer\AbstractResponseParserFactory;
use Paranoia\Acquirer\Gvp\ResponseParser\AuthorizationResponseParser;
use Paranoia\Acquirer\Gvp\ResponseParser\CancelResponseParser;
use Paranoia\Acquirer\Gvp\ResponseParser\CaptureResponseParser;
use Paranoia\Acquirer\Gvp\ResponseParser\ChargeResponseParser;
use Paranoia\Acquirer\Gvp\ResponseParser\RefundResponseParser;
use Paranoia\Core\Constant\TransactionType;
use Paranoia\Core\Exception\InvalidArgumentException;

class GvpResponseParserFactory extends AbstractResponseParserFactory
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
