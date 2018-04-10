<?php
namespace Paranoia\Test\Formatter;

use Paranoia\Formatter\Decimal;
use PHPUnit\Framework\TestCase;
use Paranoia\Exception\InvalidArgumentException;

class DecimalTest extends TestCase
{
    public function test_invalid_input_null()
    {
        $formatter = new Decimal();
        $this->expectException(InvalidArgumentException::class);
        $formatter->format(null);
    }

    public function test_invalid_input_alphanumeric()
    {
        $formatter = new Decimal();
        $this->expectException(InvalidArgumentException::class);
        $formatter->format('bad value');
    }

    public function test_valid_input()
    {
        $formatter = new Decimal();
        $this->assertEquals('1.11', $formatter->format(1.114));
        $this->assertEquals('1.12', $formatter->format(1.115));
    }
}