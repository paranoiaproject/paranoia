<?php
namespace Paranoia\Test\Formatter\Posnet;

use Paranoia\Exception\InvalidArgumentException;
use Paranoia\Formatter\Posnet\OrderId;
use PHPUnit\Framework\TestCase;

class OrderIdTest extends TestCase
{
    public function test()
    {
        $formatter = new OrderId();
        $this->assertEquals(24, strlen($formatter->format('123')));
        $this->assertEquals('000000000000000000000123', $formatter->format('123'));
        $this->assertEquals(24, strlen($formatter->format('12')));
        $this->assertEquals(24, strlen($formatter->format('123456123456123456123456')));
    }

    public function test_too_long_input()
    {
        $formatter = new OrderId();
        $this->expectException(InvalidArgumentException::class);
        $this->assertEquals(24, strlen($formatter->format('123456123456123456123456123456')));
    }
}