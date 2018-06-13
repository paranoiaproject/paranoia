<?php
namespace Paranoia\Builder;

use Paranoia\Builder\Posnet\CancelRequestBuilder;
use Paranoia\Builder\Posnet\PostAuthorizationRequestBuilder;
use Paranoia\Builder\Posnet\PreAuthorizationRequestBuilder;
use Paranoia\Builder\Posnet\RefundRequestBuilder;
use Paranoia\Builder\Posnet\SaleRequestBuilder;
use Paranoia\Exception\NotImplementedError;
use Paranoia\Formatter\MoneyFormatter;
use Paranoia\Formatter\MultiDigitInstallmentFormatter;
use Paranoia\Formatter\Posnet\CustomCurrencyCodeFormatter;
use Paranoia\Formatter\Posnet\ExpireDateFormatter;
use Paranoia\Formatter\Posnet\OrderIdFormatter;
use Paranoia\TransactionType;

class PosnetBuilderFactory extends AbstractBuilderFactory
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
                    new CustomCurrencyCodeFormatter(),
                    new MoneyFormatter(),
                    new MultiDigitInstallmentFormatter(),
                    new ExpireDateFormatter(),
                    new OrderIdFormatter()
                );
            case TransactionType::CANCEL:
                return new CancelRequestBuilder(
                    $this->configuration,
                    new CustomCurrencyCodeFormatter(),
                    new MoneyFormatter(),
                    new MultiDigitInstallmentFormatter(),
                    new ExpireDateFormatter(),
                    new OrderIdFormatter()
                );
            case TransactionType::REFUND:
                return new RefundRequestBuilder(
                    $this->configuration,
                    new CustomCurrencyCodeFormatter(),
                    new MoneyFormatter(),
                    new MultiDigitInstallmentFormatter(),
                    new ExpireDateFormatter(),
                    new OrderIdFormatter()
                );
            case TransactionType::PRE_AUTHORIZATION:
                return new PreAuthorizationRequestBuilder(
                    $this->configuration,
                    new CustomCurrencyCodeFormatter(),
                    new MoneyFormatter(),
                    new MultiDigitInstallmentFormatter(),
                    new ExpireDateFormatter(),
                    new OrderIdFormatter()
                );
            case TransactionType::POST_AUTHORIZATION:
                return new PostAuthorizationRequestBuilder(
                    $this->configuration,
                    new CustomCurrencyCodeFormatter(),
                    new MoneyFormatter(),
                    new MultiDigitInstallmentFormatter(),
                    new ExpireDateFormatter(),
                    new OrderIdFormatter()
                );
            default:
                throw new NotImplementedError('Not implemented transaction type: ' . $transactionType);
        }
    }
}
