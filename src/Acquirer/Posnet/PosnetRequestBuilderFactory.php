<?php
namespace Paranoia\Acquirer\Posnet;

use Paranoia\Acquirer\AbstractRequestBuilder;
use Paranoia\Acquirer\AbstractRequestBuilderFactory;
use Paranoia\Acquirer\Posnet\Formatter\CustomCurrencyCodeFormatter;
use Paranoia\Acquirer\Posnet\Formatter\ExpireDateFormatter;
use Paranoia\Acquirer\Posnet\Formatter\OrderIdFormatter;
use Paranoia\Acquirer\Posnet\RequestBuilder\CancelRequestBuilder;
use Paranoia\Acquirer\Posnet\RequestBuilder\ChargeRequestBuilder;
use Paranoia\Acquirer\Posnet\RequestBuilder\CaptureRequestBuilder;
use Paranoia\Acquirer\Posnet\RequestBuilder\AuthorizationRequestBuilder;
use Paranoia\Acquirer\Posnet\RequestBuilder\RefundRequestBuilder;
use Paranoia\Core\Constant\TransactionType;
use Paranoia\Core\Exception\NotImplementedError;
use Paranoia\Core\Formatter\MoneyFormatter;
use Paranoia\Core\Formatter\MultiDigitInstallmentFormatter;

class PosnetRequestBuilderFactory extends AbstractRequestBuilderFactory
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
                return new AuthorizationRequestBuilder(
                    $this->configuration,
                    new CustomCurrencyCodeFormatter(),
                    new MoneyFormatter(),
                    new MultiDigitInstallmentFormatter(),
                    new ExpireDateFormatter(),
                    new OrderIdFormatter()
                );
            case TransactionType::POST_AUTHORIZATION:
                return new CaptureRequestBuilder(
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
