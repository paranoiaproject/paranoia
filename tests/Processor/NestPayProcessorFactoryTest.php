<?php
namespace Paranoia\Test\Processor;

use Paranoia\Configuration\NestPay;
use Paranoia\Exception\InvalidArgumentException;
use Paranoia\Processor\NestPay\CancelResponseProcessor;
use Paranoia\Processor\NestPay\PostAuthorizationResponseProcessor;
use Paranoia\Processor\NestPay\PreAuthorizationResponseProcessor;
use Paranoia\Processor\NestPay\RefundResponseProcessor;
use Paranoia\Processor\NestPay\SaleResponseProcessor;
use Paranoia\Processor\NestPayProcessorFactory;
use Paranoia\TransactionType;
use PHPUnit\Framework\TestCase;

class NestPayProcessorFactoryTest extends TestCase
{
    public function test_valid_transaction_type()
    {
        /** @var NestPay $configuration */
        $configuration = $this->getMockBuilder(NestPay::class)->getMock();
        $factory = new NestPayProcessorFactory($configuration);
        $this->assertInstanceOf(SaleResponseProcessor::class, $factory->createProcessor(TransactionType::SALE));
        $this->assertInstanceOf(RefundResponseProcessor::class, $factory->createProcessor(TransactionType::REFUND));
        $this->assertInstanceOf(CancelResponseProcessor::class, $factory->createProcessor(TransactionType::CANCEL));
        $this->assertInstanceOf(PreAuthorizationResponseProcessor::class, $factory->createProcessor(TransactionType::PRE_AUTHORIZATION));
        $this->assertInstanceOf(PostAuthorizationResponseProcessor::class, $factory->createProcessor(TransactionType::POST_AUTHORIZATION));
    }

    public function test_invalid_transaction_type()
    {
        $this->expectException(InvalidArgumentException::class);

        /** @var NestPay $configuration */
        $configuration = $this->getMockBuilder(NestPay::class)->getMock();

        $factory = new NestPayProcessorFactory($configuration);
        $factory->createProcessor('dummy');
    }
}