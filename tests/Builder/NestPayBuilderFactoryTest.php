<?php
namespace Paranoia\Test\Builder;

use Paranoia\Builder\NestPay\CancelRequestBuilder;
use Paranoia\Builder\NestPay\PostAuthorizationRequestBuilder;
use Paranoia\Builder\NestPay\PreAuthorizationRequestBuilder;
use Paranoia\Builder\NestPay\RefundRequestBuilder;
use Paranoia\Builder\NestPay\SaleRequestBuilder;
use Paranoia\Builder\NestPayBuilderFactory;
use Paranoia\Configuration\NestPay as NestPayConfiguration;
use Paranoia\Exception\NotImplementedError;
use Paranoia\TransactionType;
use PHPUnit\Framework\TestCase;


class NestPayBuilderFactoryTest extends TestCase
{
    public function test_valid_transaction_types()
    {
        /** @var NestPayConfiguration $configuration */
        $configuration = $this->getMockBuilder(NestPayConfiguration::class)->getMock();

        $factory = new NestPayBuilderFactory($configuration);
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

        $factory = new NestPayBuilderFactory($configuration);
        $factory->createBuilder('Dummy');
    }
}
