<?php
namespace Paranoia\Acquirer\Gvp;

use Paranoia\Acquirer\Gvp\RequestBuilder\CancelRequestBuilder;
use Paranoia\Acquirer\Gvp\RequestBuilder\PostAuthorizationRequestBuilder;
use Paranoia\Acquirer\Gvp\RequestBuilder\PreAuthorizationRequestBuilder;
use Paranoia\Acquirer\Gvp\RequestBuilder\RefundRequestBuilder;
use Paranoia\Acquirer\Gvp\RequestBuilder\SaleRequestBuilder;
use Paranoia\Acquirer\AbstractRequestBuilderFactory;
use Paranoia\Acquirer\AbstractRequestBuilder;
use Paranoia\Core\Exception\NotImplementedError;
use Paranoia\Acquirer\Gvp\Formatter\ExpireDateFormatter;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use Paranoia\Core\Constant\TransactionType;

class GvpRequestBuilderFactory extends AbstractRequestBuilderFactory
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
