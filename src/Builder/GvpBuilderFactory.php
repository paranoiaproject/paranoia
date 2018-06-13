<?php
namespace Paranoia\Builder;

use Paranoia\Builder\Gvp\CancelRequestBuilder;
use Paranoia\Builder\Gvp\PostAuthorizationRequestBuilder;
use Paranoia\Builder\Gvp\PreAuthorizationRequestBuilder;
use Paranoia\Builder\Gvp\RefundRequestBuilder;
use Paranoia\Builder\Gvp\SaleRequestBuilder;
use Paranoia\Exception\NotImplementedError;
use Paranoia\Formatter\Gvp\ExpireDateFormatter;
use Paranoia\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Formatter\MoneyFormatter;
use Paranoia\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\TransactionType;

class GvpBuilderFactory extends AbstractBuilderFactory
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
                return new SaleRequestBuilder(
                    $this->configuration,
                    new IsoNumericCurrencyCodeFormatter(),
                    new MoneyFormatter(),
                    new SingleDigitInstallmentFormatter(),
                    new ExpireDateFormatter()
                );
            case TransactionType::CANCEL:
                return new CancelRequestBuilder(
                    $this->configuration,
                    new IsoNumericCurrencyCodeFormatter(),
                    new MoneyFormatter(),
                    new SingleDigitInstallmentFormatter(),
                    new ExpireDateFormatter()
                );
            case TransactionType::REFUND:
                return new RefundRequestBuilder(
                    $this->configuration,
                    new IsoNumericCurrencyCodeFormatter(),
                    new MoneyFormatter(),
                    new SingleDigitInstallmentFormatter(),
                    new ExpireDateFormatter()
                );
            case TransactionType::PRE_AUTHORIZATION:
                return new PreAuthorizationRequestBuilder(
                    $this->configuration,
                    new IsoNumericCurrencyCodeFormatter(),
                    new MoneyFormatter(),
                    new SingleDigitInstallmentFormatter(),
                    new ExpireDateFormatter()
                );
            case TransactionType::POST_AUTHORIZATION:
                return new PostAuthorizationRequestBuilder(
                    $this->configuration,
                    new IsoNumericCurrencyCodeFormatter(),
                    new MoneyFormatter(),
                    new SingleDigitInstallmentFormatter(),
                    new ExpireDateFormatter()
                );
            default:
                throw new NotImplementedError('Not implemented transaction type: ' . $transactionType);
        }
    }
}
