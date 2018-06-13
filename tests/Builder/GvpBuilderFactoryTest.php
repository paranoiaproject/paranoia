<?php
namespace Paranoia\Test\Builder;

use Paranoia\Builder\Gvp\CancelRequestBuilder;
use Paranoia\Builder\Gvp\PostAuthorizationRequestBuilder;
use Paranoia\Builder\Gvp\PreAuthorizationRequestBuilder;
use Paranoia\Builder\Gvp\RefundRequestBuilder;
use Paranoia\Builder\Gvp\SaleRequestBuilder;
use Paranoia\Builder\GvpBuilderFactory;
use Paranoia\Configuration\Gvp as GvpConfiguration;
use Paranoia\Exception\NotImplementedError;
use Paranoia\TransactionType;
use PHPUnit\Framework\TestCase;

class GvpBuilderFactoryTest extends TestCase
{
    public function test_valid_transaction_types()
    {
        /** @var GvpConfiguration $configuration */
        $configuration = $this->getMockBuilder(GvpConfiguration::class)->getMock();
        $factory = new GvpBuilderFactory($configuration);
        $this->assertInstanceOf(SaleRequestBuilder::class, $factory->createBuilder(TransactionType::SALE));
        $this->assertInstanceOf(RefundRequestBuilder::class, $factory->createBuilder(TransactionType::REFUND));
        $this->assertInstanceOf(CancelRequestBuilder::class, $factory->createBuilder(TransactionType::CANCEL));
        $this->assertInstanceOf(PreAuthorizationRequestBuilder::class, $factory->createBuilder(TransactionType::PRE_AUTHORIZATION));
        $this->assertInstanceOf(PostAuthorizationRequestBuilder::class, $factory->createBuilder(TransactionType::POST_AUTHORIZATION));
    }

    public function test_invalid_transaction_type()
    {
        $this->expectException(NotImplementedError::class);

        /** @var GvpConfiguration $configuration */
        $configuration = $this->getMockBuilder(GvpConfiguration::class)->getMock();

        $factory = new GvpBuilderFactory($configuration);
        $factory->createBuilder('Dummy');
    }
}
