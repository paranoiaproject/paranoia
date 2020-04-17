<?php
namespace Paranoia\Test\Unit\Core\Formatter;

use Paranoia\Core\Formatter\DecimalFormatter;
use PHPUnit\Framework\TestCase;
use Paranoia\Core\Exception\InvalidArgumentException;

class DecimalFormatterTest extends TestCase
{
    public function test_invalid_input_null(): void
    {
        $formatter = new DecimalFormatter();
        $this->expectException(InvalidArgumentException::class);
        $formatter->format(null);
    }

    public function test_valid_input(): void
    {
        $formatter = new DecimalFormatter();
        $this->assertEquals('1.11', $formatter->format(1.114));
        $this->assertEquals('1.12', $formatter->format(1.115));
    }
}
