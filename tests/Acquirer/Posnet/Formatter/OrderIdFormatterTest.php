<?php
namespace Paranoia\Test\Acquirer\Posnet\Formatter;

use Paranoia\Core\Exception\InvalidArgumentException;
use Paranoia\Acquirer\Posnet\Formatter\OrderIdFormatter;
use PHPUnit\Framework\TestCase;

class OrderIdFormatterTest extends TestCase
{
    public function test()
    {
        $formatter = new OrderIdFormatter();
        $this->assertEquals(24, strlen($formatter->format('123')));
        $this->assertEquals('000000000000000000000123', $formatter->format('123'));
        $this->assertEquals(24, strlen($formatter->format('12')));
        $this->assertEquals(24, strlen($formatter->format('123456123456123456123456')));
    }

    public function test_too_long_input()
    {
        $formatter = new OrderIdFormatter();
        $this->expectException(InvalidArgumentException::class);
        $this->assertEquals(24, strlen($formatter->format('123456123456123456123456123456')));
    }
}