<?php
namespace Paranoia\Test\Formatter;

use Paranoia\Exception\InvalidArgumentException;
use Paranoia\Formatter\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_invalid_input_null()
    {
        $formatter = new Money();
        $this->expectException(InvalidArgumentException::class);
        $formatter->format(null);
    }

    public function test_invalid_input_alphanumeric()
    {
        $formatter = new Money();
        $this->expectException(InvalidArgumentException::class);
        $formatter->format('bad value');
    }

    public function test_valid_input()
    {
        $formatter = new Money();
        $this->assertEquals(120, $formatter->format(1.2));
        $this->assertEquals(12, $formatter->format(0.12));
    }
}
