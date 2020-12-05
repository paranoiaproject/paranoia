<?php
namespace Paranoia\Test\Acquirer\Gvp;

use Paranoia\Acquirer\Gvp\RequestBuilder\CancelRequestBuilder;
use Paranoia\Acquirer\Gvp\RequestBuilder\PostAuthorizationRequestBuilder;
use Paranoia\Acquirer\Gvp\RequestBuilder\PreAuthorizationRequestBuilder;
use Paranoia\Acquirer\Gvp\RequestBuilder\RefundRequestBuilder;
use Paranoia\Acquirer\Gvp\RequestBuilder\SaleRequestBuilder;
use Paranoia\Acquirer\Gvp\GvpRequestBuilderFactory;
use Paranoia\Acquirer\Gvp\GvpConfiguration as GvpConfiguration;
use Paranoia\Core\Exception\NotImplementedError;
use Paranoia\Core\Constant\TransactionType;
use PHPUnit\Framework\TestCase;

class GvpBuilderFactoryTest extends TestCase
{
    public function test_valid_transaction_types()
    {
        /** @var GvpConfiguration $configuration */
        $configuration = $this->getMockBuilder(GvpConfiguration::class)->getMock();
        $factory = new GvpRequestBuilderFactory($configuration);
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

        $factory = new GvpRequestBuilderFactory($configuration);
        $factory->createBuilder('Dummy');
    }
}
