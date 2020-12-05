<?php
namespace Paranoia\Test\Acquirer\NestPay;

use Paranoia\Acquirer\NestPay\NestPayConfiguration as NestPayConfiguration;
use Paranoia\Acquirer\NestPay\NestPayRequestBuilderFactory;
use Paranoia\Acquirer\NestPay\RequestBuilder\AuthorizationRequestBuilder;
use Paranoia\Acquirer\NestPay\RequestBuilder\CancelRequestBuilder;
use Paranoia\Acquirer\NestPay\RequestBuilder\CaptureRequestBuilder;
use Paranoia\Acquirer\NestPay\RequestBuilder\ChargeRequestBuilder;
use Paranoia\Acquirer\NestPay\RequestBuilder\RefundRequestBuilder;
use Paranoia\Core\Constant\TransactionType;
use Paranoia\Core\Exception\NotImplementedError;
use PHPUnit\Framework\TestCase;


class NestPayBuilderFactoryTest extends TestCase
{
    public function test_valid_transaction_types()
    {
        /** @var NestPayConfiguration $configuration */
        $configuration = $this->getMockBuilder(NestPayConfiguration::class)->getMock();

        $factory = new NestPayRequestBuilderFactory($configuration);
        $this->assertInstanceOf(ChargeRequestBuilder::class, $factory->createBuilder(TransactionType::SALE));
        $this->assertInstanceOf(RefundRequestBuilder::class, $factory->createBuilder(TransactionType::REFUND));
        $this->assertInstanceOf(CancelRequestBuilder::class, $factory->createBuilder(TransactionType::CANCEL));
        $this->assertInstanceOf(AuthorizationRequestBuilder::class, $factory->createBuilder(TransactionType::PRE_AUTHORIZATION));
        $this->assertInstanceOf(CaptureRequestBuilder::class, $factory->createBuilder(TransactionType::POST_AUTHORIZATION));
    }

    public function test_invalid_transaction_type()
    {
        $this->expectException(NotImplementedError::class);

        /** @var NestPayConfiguration $configuration */
        $configuration = $this->getMockBuilder(NestPayConfiguration::class)->getMock();

        $factory = new NestPayRequestBuilderFactory($configuration);
        $factory->createBuilder('Dummy');
    }
}
