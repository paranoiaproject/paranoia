<?php
namespace Paranoia\Test\Acquirer\Posnet;

use Paranoia\Acquirer\Posnet\PosnetConfiguration;
use Paranoia\Core\Exception\InvalidArgumentException;
use Paranoia\Acquirer\Posnet\ResponseParser\CancelResponseParser;
use Paranoia\Acquirer\Posnet\ResponseParser\PostAuthorizationResponseParser;
use Paranoia\Acquirer\Posnet\ResponseParser\PreAuthorizationResponseParser;
use Paranoia\Acquirer\Posnet\ResponseParser\RefundResponseParser;
use Paranoia\Acquirer\Posnet\ResponseParser\SaleResponseParser;
use Paranoia\Acquirer\Posnet\PosnetResponseParserFactory;
use Paranoia\Core\Constant\TransactionType;
use PHPUnit\Framework\TestCase;

class PosnetProcessorFactoryTest extends TestCase
{
    public function test_valid_transaction_type()
    {
        /** @var PosnetConfiguration $configuration */
        $configuration = $this->getMockBuilder(PosnetConfiguration::class)->getMock();
        $factory = new PosnetResponseParserFactory($configuration);
        $this->assertInstanceOf(SaleResponseParser::class, $factory->createProcessor(TransactionType::SALE));
        $this->assertInstanceOf(RefundResponseParser::class, $factory->createProcessor(TransactionType::REFUND));
        $this->assertInstanceOf(CancelResponseParser::class, $factory->createProcessor(TransactionType::CANCEL));
        $this->assertInstanceOf(PreAuthorizationResponseParser::class, $factory->createProcessor(TransactionType::PRE_AUTHORIZATION));
        $this->assertInstanceOf(PostAuthorizationResponseParser::class, $factory->createProcessor(TransactionType::POST_AUTHORIZATION));
    }

    public function test_invalid_transaction_type()
    {
        $this->expectException(InvalidArgumentException::class);

        /** @var PosnetConfiguration $configuration */
        $configuration = $this->getMockBuilder(PosnetConfiguration::class)->getMock();

        $factory = new PosnetResponseParserFactory($configuration);
        $factory->createProcessor('dummy');
    }
}