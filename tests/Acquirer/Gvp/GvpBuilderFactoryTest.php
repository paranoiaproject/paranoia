<?php
namespace Paranoia\Test\Acquirer\Gvp;

use Paranoia\Acquirer\Gvp\GvpConfiguration as GvpConfiguration;
use Paranoia\Acquirer\Gvp\GvpRequestBuilderFactory;
use Paranoia\Acquirer\Gvp\RequestBuilder\CancelRequestBuilder;
use Paranoia\Acquirer\Gvp\RequestBuilder\ChargeRequestBuilder;
use Paranoia\Acquirer\Gvp\RequestBuilder\CaptureRequestBuilder;
use Paranoia\Acquirer\Gvp\RequestBuilder\AuthorizationRequestBuilder;
use Paranoia\Acquirer\Gvp\RequestBuilder\RefundRequestBuilder;
use Paranoia\Core\Constant\TransactionType;
use Paranoia\Core\Exception\NotImplementedError;
use PHPUnit\Framework\TestCase;

class GvpBuilderFactoryTest extends TestCase
{
    public function test_valid_transaction_types()
    {
        /** @var GvpConfiguration $configuration */
        $configuration = $this->getMockBuilder(GvpConfiguration::class)->getMock();
        $factory = new GvpRequestBuilderFactory($configuration);
        $this->assertInstanceOf(ChargeRequestBuilder::class, $factory->createBuilder(TransactionType::SALE));
        $this->assertInstanceOf(RefundRequestBuilder::class, $factory->createBuilder(TransactionType::REFUND));
        $this->assertInstanceOf(CancelRequestBuilder::class, $factory->createBuilder(TransactionType::CANCEL));
        $this->assertInstanceOf(AuthorizationRequestBuilder::class, $factory->createBuilder(TransactionType::PRE_AUTHORIZATION));
        $this->assertInstanceOf(CaptureRequestBuilder::class, $factory->createBuilder(TransactionType::POST_AUTHORIZATION));
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
