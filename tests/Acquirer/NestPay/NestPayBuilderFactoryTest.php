<?php
namespace Paranoia\Test\Acquirer\NestPay;

use Paranoia\Acquirer\NestPay\RequestBuilder\CancelRequestBuilder;
use Paranoia\Acquirer\NestPay\RequestBuilder\PostAuthorizationRequestBuilder;
use Paranoia\Acquirer\NestPay\RequestBuilder\PreAuthorizationRequestBuilder;
use Paranoia\Acquirer\NestPay\RequestBuilder\RefundRequestBuilder;
use Paranoia\Acquirer\NestPay\RequestBuilder\SaleRequestBuilder;
use Paranoia\Acquirer\NestPay\NestPayRequestBuilderFactory;
use Paranoia\Acquirer\NestPay\NestPayConfiguration as NestPayConfiguration;
use Paranoia\Core\Exception\NotImplementedError;
use Paranoia\Core\Constant\TransactionType;
use PHPUnit\Framework\TestCase;


class NestPayBuilderFactoryTest extends TestCase
{
    public function test_valid_transaction_types()
    {
        /** @var NestPayConfiguration $configuration */
        $configuration = $this->getMockBuilder(NestPayConfiguration::class)->getMock();

        $factory = new NestPayRequestBuilderFactory($configuration);
        $this->assertInstanceOf(SaleRequestBuilder::class, $factory->createBuilder(TransactionType::SALE));
        $this->assertInstanceOf(RefundRequestBuilder::class, $factory->createBuilder(TransactionType::REFUND));
        $this->assertInstanceOf(CancelRequestBuilder::class, $factory->createBuilder(TransactionType::CANCEL));
        $this->assertInstanceOf(PreAuthorizationRequestBuilder::class, $factory->createBuilder(TransactionType::PRE_AUTHORIZATION));
        $this->assertInstanceOf(PostAuthorizationRequestBuilder::class, $factory->createBuilder(TransactionType::POST_AUTHORIZATION));
    }

    public function test_invalid_transaction_type()
    {
        $this->expectException(NotImplementedError::class);

        /** @var NestPayConfiguration $configuration */
        $configuration = $this->getMockBuilder(NestPayConfiguration::class)->getMock();

        $factory = new NestPayRequestBuilderFactory($configuration);
        $factory->createBuilder('Dummy');
    }
}
