<?php
namespace Paranoia\Test\Acquirer\NestPay;

use Paranoia\Acquirer\NestPay\NestPayConfiguration;
use Paranoia\Acquirer\NestPay\NestPayResponseParserFactory;
use Paranoia\Acquirer\NestPay\ResponseParser\CancelResponseParser;
use Paranoia\Acquirer\NestPay\ResponseParser\ChargeResponseParser;
use Paranoia\Acquirer\NestPay\ResponseParser\PostAuthorizationResponseParser;
use Paranoia\Acquirer\NestPay\ResponseParser\PreAuthorizationResponseParser;
use Paranoia\Acquirer\NestPay\ResponseParser\RefundResponseParser;
use Paranoia\Core\Constant\TransactionType;
use Paranoia\Core\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class NestPayProcessorFactoryTest extends TestCase
{
    public function test_valid_transaction_type()
    {
        /** @var NestPayConfiguration $configuration */
        $configuration = $this->getMockBuilder(NestPayConfiguration::class)->getMock();
        $factory = new NestPayResponseParserFactory($configuration);
        $this->assertInstanceOf(ChargeResponseParser::class, $factory->createProcessor(TransactionType::SALE));
        $this->assertInstanceOf(RefundResponseParser::class, $factory->createProcessor(TransactionType::REFUND));
        $this->assertInstanceOf(CancelResponseParser::class, $factory->createProcessor(TransactionType::CANCEL));
        $this->assertInstanceOf(PreAuthorizationResponseParser::class, $factory->createProcessor(TransactionType::PRE_AUTHORIZATION));
        $this->assertInstanceOf(PostAuthorizationResponseParser::class, $factory->createProcessor(TransactionType::POST_AUTHORIZATION));
    }

    public function test_invalid_transaction_type()
    {
        $this->expectException(InvalidArgumentException::class);

        /** @var NestPayConfiguration $configuration */
        $configuration = $this->getMockBuilder(NestPayConfiguration::class)->getMock();

        $factory = new NestPayResponseParserFactory($configuration);
        $factory->createProcessor('dummy');
    }
}