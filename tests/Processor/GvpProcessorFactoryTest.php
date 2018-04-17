<?php
namespace Paranoia\Test\Processor;

use Paranoia\Configuration\Gvp;
use Paranoia\Exception\InvalidArgumentException;
use Paranoia\Processor\Gvp\CancelResponseProcessor;
use Paranoia\Processor\Gvp\PostAuthorizationResponseProcessor;
use Paranoia\Processor\Gvp\PreAuthorizationResponseProcessor;
use Paranoia\Processor\Gvp\RefundResponseProcessor;
use Paranoia\Processor\Gvp\SaleResponseProcessor;
use Paranoia\Processor\GvpProcessorFactory;
use Paranoia\TransactionType;
use PHPUnit\Framework\TestCase;

class GvpProcessorFactoryTest extends TestCase
{
    public function test_valid_transaction_type()
    {
        /** @var Gvp $configuration */
        $configuration = $this->getMockBuilder(Gvp::class)->getMock();
        $factory = new GvpProcessorFactory($configuration);
        $this->assertInstanceOf(SaleResponseProcessor::class, $factory->createProcessor(TransactionType::SALE));
        $this->assertInstanceOf(RefundResponseProcessor::class, $factory->createProcessor(TransactionType::REFUND));
        $this->assertInstanceOf(CancelResponseProcessor::class, $factory->createProcessor(TransactionType::CANCEL));
        $this->assertInstanceOf(PreAuthorizationResponseProcessor::class, $factory->createProcessor(TransactionType::PRE_AUTHORIZATION));
        $this->assertInstanceOf(PostAuthorizationResponseProcessor::class, $factory->createProcessor(TransactionType::POST_AUTHORIZATION));
    }

    public function test_invalid_transaction_type()
    {
        $this->expectException(InvalidArgumentException::class);

        /** @var Gvp $configuration */
        $configuration = $this->getMockBuilder(Gvp::class)->getMock();

        $factory = new GvpProcessorFactory($configuration);
        $factory->createProcessor('dummy');
    }
}