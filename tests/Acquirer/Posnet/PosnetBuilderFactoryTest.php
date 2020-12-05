<?php
namespace Paranoia\Test\Acquirer\Posnet;

use Paranoia\Acquirer\Posnet\PosnetConfiguration as PosnetConfiguration;
use Paranoia\Acquirer\Posnet\PosnetRequestBuilderFactory;
use Paranoia\Acquirer\Posnet\RequestBuilder\CancelRequestBuilder;
use Paranoia\Acquirer\Posnet\RequestBuilder\ChargeRequestBuilder;
use Paranoia\Acquirer\Posnet\RequestBuilder\PostAuthorizationRequestBuilder;
use Paranoia\Acquirer\Posnet\RequestBuilder\PreAuthorizationRequestBuilder;
use Paranoia\Acquirer\Posnet\RequestBuilder\RefundRequestBuilder;
use Paranoia\Core\Constant\TransactionType;
use Paranoia\Core\Exception\NotImplementedError;
use PHPUnit\Framework\TestCase;

class PosnetBuilderFactoryTest extends TestCase
{
    public function test_valid_transaction_types()
    {
        /** @var PosnetConfiguration $configuration */
        $configuration = $this->getMockBuilder(PosnetConfiguration::class)->getMock();

        $factory = new PosnetRequestBuilderFactory($configuration);
        $this->assertInstanceOf(ChargeRequestBuilder::class, $factory->createBuilder(TransactionType::SALE));
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

        $factory = new PosnetRequestBuilderFactory($configuration);
        $factory->createBuilder('Dummy');
    }

    protected function setupConfiguration()
    {
        $configuration = new PosnetConfiguration();;
        return $configuration;
    }
}
