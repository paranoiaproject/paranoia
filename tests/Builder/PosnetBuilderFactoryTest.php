<?php
namespace Paranoia\Test\Builder;

use Paranoia\Builder\Posnet\CancelRequestBuilder;
use Paranoia\Builder\Posnet\PostAuthorizationRequestBuilder;
use Paranoia\Builder\Posnet\PreAuthorizationRequestBuilder;
use Paranoia\Builder\Posnet\RefundRequestBuilder;
use Paranoia\Builder\Posnet\SaleRequestBuilder;
use Paranoia\Builder\PosnetBuilderFactory;
use Paranoia\Configuration\Posnet as PosnetConfiguration;
use Paranoia\Exception\NotImplementedError;
use Paranoia\TransactionType;
use PHPUnit\Framework\TestCase;

class PosnetBuilderFactoryTest extends TestCase
{
    public function test_valid_transaction_types()
    {
        /** @var PosnetConfiguration $configuration */
        $configuration = $this->getMockBuilder(PosnetConfiguration::class)->getMock();

        $factory = new PosnetBuilderFactory($configuration);
        $this->assertInstanceOf(SaleRequestBuilder::class, $factory->createBuilder(TransactionType::SALE));
        $this->assertInstanceOf(RefundRequestBuilder::class, $factory->createBuilder(TransactionType::REFUND));
        $this->assertInstanceOf(CancelRequestBuilder::class, $factory->createBuilder(TransactionType::CANCEL));
        $this->assertInstanceOf(PreAuthorizationRequestBuilder::class, $factory->createBuilder(TransactionType::PRE_AUTHORIZATION));
        $this->assertInstanceOf(PostAuthorizationRequestBuilder::class, $factory->createBuilder(TransactionType::POST_AUTHORIZATION));
    }

    public function test_invalid_transaction_type()
    {
        $this->expectException(NotImplementedError::class);

        /** @var PosnetConfiguration $configuration */
        $configuration = $this->getMockBuilder(PosnetConfiguration::class)->getMock();

        $factory = new PosnetBuilderFactory($configuration);
        $factory->createBuilder('Dummy');
    }

    protected function setupConfiguration()
    {
        $configuration = new PosnetConfiguration();;
        return $configuration;
    }
}
