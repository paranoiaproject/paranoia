<?php
namespace Paranoia\Test\Unit\PosnetFormatter\Posnet;

use Paranoia\Core\Exception\InvalidArgumentException;
use Paranoia\Posnet\Formatter\OrderIdFormatter;
use PHPUnit\Framework\TestCase;

class OrderIdFormatterTest extends TestCase
{
    public function test(): void
    {
        $formatter = new OrderIdFormatter();
        $this->assertEquals(24, strlen($formatter->format('123')));
        $this->assertEquals('000000000000000000000123', $formatter->format('123'));
        $this->assertEquals(24, strlen($formatter->format('12')));
        $this->assertEquals(24, strlen($formatter->format('123456123456123456123456')));
    }

    public function test_too_long_input(): void
    {
        $formatter = new OrderIdFormatter();
        $this->expectException(InvalidArgumentException::class);
        $this->assertEquals(24, strlen($formatter->format('123456123456123456123456123456')));
    }
}
