<?php
namespace Paranoia\Test\Unit\Nestpay\Formatter;

use Paranoia\Nestpay\Formatter\ExpireDateFormatter;
use PHPUnit\Framework\TestCase;

class ExpireDateTest extends TestCase
{
    public function test()
    {
        $formatter = new ExpireDateFormatter();
        $this->assertEquals('02/1994', $formatter->format(2, 1994));
        $this->assertEquals('12/2020', $formatter->format(12, 2020));
    }
}
