<?php
namespace Paranoia\Acquirer\NestPay;

use Paranoia\Acquirer\AbstractRequestBuilder;
use Paranoia\Acquirer\AbstractRequestBuilderFactory;
use Paranoia\Acquirer\NestPay\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\NestPay\RequestBuilder\AuthorizationRequestBuilder;
use Paranoia\Acquirer\NestPay\RequestBuilder\CancelRequestBuilder;
use Paranoia\Acquirer\NestPay\RequestBuilder\CaptureRequestBuilder;
use Paranoia\Acquirer\NestPay\RequestBuilder\ChargeRequestBuilder;
use Paranoia\Acquirer\NestPay\RequestBuilder\RefundRequestBuilder;
use Paranoia\Core\Constant\TransactionType;
use Paranoia\Core\Exception\NotImplementedError;
use Paranoia\Core\Formatter\DecimalFormatter;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;

class NestPayRequestBuilderFactory extends AbstractRequestBuilderFactory
{
    /**
     * @param $transactionType
     * @return AbstractRequestBuilder
     * @throws NotImplementedError
     */
    public function createBuilder($transactionType)
    {
        switch ($transactionType) {
            case TransactionType::SALE:
                return new ChargeRequestBuilder(
                    $this->configuration,
                    new IsoNumericCurrencyCodeFormatter(),
                    new DecimalFormatter(),
                    new SingleDigitInstallmentFormatter(),
                    new ExpireDateFormatter()
                );
            case TransactionType::CANCEL:
                return new CancelRequestBuilder(
                    $this->configuration,
                    new IsoNumericCurrencyCodeFormatter(),
                    new DecimalFormatter(),
                    new SingleDigitInstallmentFormatter(),
                    new ExpireDateFormatter()
                );
            case TransactionType::REFUND:
                return new RefundRequestBuilder(
                    $this->configuration,
                    new IsoNumericCurrencyCodeFormatter(),
                    new DecimalFormatter(),
                    new SingleDigitInstallmentFormatter(),
                    new ExpireDateFormatter()
                );
            case TransactionType::PRE_AUTHORIZATION:
                return new AuthorizationRequestBuilder(
                    $this->configuration,
                    new IsoNumericCurrencyCodeFormatter(),
                    new DecimalFormatter(),
                    new SingleDigitInstallmentFormatter(),
                    new ExpireDateFormatter()
                );
            case TransactionType::POST_AUTHORIZATION:
                return new CaptureRequestBuilder(
                    $this->configuration,
                    new IsoNumericCurrencyCodeFormatter(),
                    new DecimalFormatter(),
                    new SingleDigitInstallmentFormatter(),
                    new ExpireDateFormatter()
                );
            default:
                throw new NotImplementedError('Not implemented transaction type: ' . $transactionType);
        }
    }
}
