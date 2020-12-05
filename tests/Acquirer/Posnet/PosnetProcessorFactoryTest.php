<?php
namespace Paranoia\Test\Acquirer\Posnet;

use Paranoia\Acquirer\Posnet\PosnetConfiguration;
use Paranoia\Acquirer\Posnet\PosnetResponseParserFactory;
use Paranoia\Acquirer\Posnet\ResponseParser\CancelResponseParser;
use Paranoia\Acquirer\Posnet\ResponseParser\ChargeResponseParser;
use Paranoia\Acquirer\Posnet\ResponseParser\CaptureResponseParser;
use Paranoia\Acquirer\Posnet\ResponseParser\AuthorizationResponseParser;
use Paranoia\Acquirer\Posnet\ResponseParser\RefundResponseParser;
use Paranoia\Core\Constant\TransactionType;
use Paranoia\Core\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PosnetProcessorFactoryTest extends TestCase
{
    public function test_valid_transaction_type()
    {
        /** @var PosnetConfiguration $configuration */
        $configuration = $this->getMockBuilder(PosnetConfiguration::class)->getMock();
        $factory = new PosnetResponseParserFactory($configuration);
        $this->assertInstanceOf(ChargeResponseParser::class, $factory->createProcessor(TransactionType::SALE));
        $this->assertInstanceOf(RefundResponseParser::class, $factory->createProcessor(TransactionType::REFUND));
        $this->assertInstanceOf(CancelResponseParser::class, $factory->createProcessor(TransactionType::CANCEL));
        $this->assertInstanceOf(AuthorizationResponseParser::class, $factory->createProcessor(TransactionType::PRE_AUTHORIZATION));
        $this->assertInstanceOf(CaptureResponseParser::class, $factory->createProcessor(TransactionType::POST_AUTHORIZATION));
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