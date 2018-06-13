<?php
namespace Paranoia\Test\Formatter;

use Paranoia\Formatter\DecimalFormatter;
use PHPUnit\Framework\TestCase;
use Paranoia\Exception\InvalidArgumentException;

class DecimalFormatterTest extends TestCase
{
    public function test_invalid_input_null()
    {
        $formatter = new DecimalFormatter();
        $this->expectException(InvalidArgumentException::class);
        $formatter->format(null);
    }

    public function test_invalid_input_alphanumeric()
    {
        $formatter = new DecimalFormatter();
        $this->expectException(InvalidArgumentException::class);
        $formatter->format('bad value');
    }

    public function test_valid_input()
    {
        $formatter = new DecimalFormatter();
        $this->assertEquals('1.11', $formatter->format(1.114));
        $this->assertEquals('1.12', $formatter->format(1.115));
    }
}