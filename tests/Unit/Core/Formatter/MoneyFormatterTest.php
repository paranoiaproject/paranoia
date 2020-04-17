<?php
namespace Paranoia\Test\Unit\Core\Formatter;

use Paranoia\Core\Exception\InvalidArgumentException;
use Paranoia\Core\Formatter\MoneyFormatter;
use PHPUnit\Framework\TestCase;

class MoneyFormatterTest extends TestCase
{
    public function test_invalid_input_null(): void
    {
        $formatter = new MoneyFormatter();
        $this->expectException(InvalidArgumentException::class);
        $formatter->format(null);
    }

    public function test_valid_input(): void
    {
        $formatter = new MoneyFormatter();
        $this->assertEquals(120, $formatter->format(1.2));
        $this->assertEquals(12, $formatter->format(0.12));
    }
}
