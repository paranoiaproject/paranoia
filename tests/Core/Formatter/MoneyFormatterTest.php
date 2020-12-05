<?php
namespace Paranoia\Test\Core\Formatter;

use Paranoia\Core\Exception\InvalidArgumentException;
use Paranoia\Core\Formatter\MoneyFormatter;
use PHPUnit\Framework\TestCase;

class MoneyFormatterTest extends TestCase
{
    public function test_invalid_input_null()
    {
        $formatter = new MoneyFormatter();
        $this->expectException(InvalidArgumentException::class);
        $formatter->format(null);
    }

    public function test_invalid_input_alphanumeric()
    {
        $formatter = new MoneyFormatter();
        $this->expectException(InvalidArgumentException::class);
        $formatter->format('bad value');
    }

    public function test_valid_input()
    {
        $formatter = new MoneyFormatter();
        $this->assertEquals(120, $formatter->format(1.2));
        $this->assertEquals(12, $formatter->format(0.12));
    }
}
