<?php
namespace Paranoia\Test\Acquirer\Gvp;

use Paranoia\Acquirer\Gvp\GvpConfiguration;
use Paranoia\Acquirer\Gvp\GvpResponseParserFactory;
use Paranoia\Acquirer\Gvp\ResponseParser\AuthorizationResponseParser;
use Paranoia\Acquirer\Gvp\ResponseParser\CancelResponseParser;
use Paranoia\Acquirer\Gvp\ResponseParser\CaptureResponseParser;
use Paranoia\Acquirer\Gvp\ResponseParser\ChargeResponseParser;
use Paranoia\Acquirer\Gvp\ResponseParser\RefundResponseParser;
use Paranoia\Core\Constant\TransactionType;
use Paranoia\Core\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class GvpProcessorFactoryTest extends TestCase
{
    public function test_valid_transaction_type()
    {
        /** @var GvpConfiguration $configuration */
        $configuration = $this->getMockBuilder(GvpConfiguration::class)->getMock();
        $factory = new GvpResponseParserFactory($configuration);
        $this->assertInstanceOf(ChargeResponseParser::class, $factory->createProcessor(TransactionType::SALE));
        $this->assertInstanceOf(RefundResponseParser::class, $factory->createProcessor(TransactionType::REFUND));
        $this->assertInstanceOf(CancelResponseParser::class, $factory->createProcessor(TransactionType::CANCEL));
        $this->assertInstanceOf(AuthorizationResponseParser::class, $factory->createProcessor(TransactionType::PRE_AUTHORIZATION));
        $this->assertInstanceOf(CaptureResponseParser::class, $factory->createProcessor(TransactionType::POST_AUTHORIZATION));
    }

    public function test_invalid_transaction_type()
    {
        $this->expectException(InvalidArgumentException::class);

        /** @var GvpConfiguration $configuration */
        $configuration = $this->getMockBuilder(GvpConfiguration::class)->getMock();

        $factory = new GvpResponseParserFactory($configuration);
        $factory->createProcessor('dummy');
    }
}