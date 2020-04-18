<?php
namespace Paranoia\Test\Unit\Gvp\Formatter;

use Paranoia\Gvp\Formatter\ExpireDateFormatter;
use PHPUnit\Framework\TestCase;

class ExpireDateTest extends TestCase
{
    public function test()
    {
        $formatter = new ExpireDateFormatter();
        $this->assertEquals('0294', $formatter->format(2, 1994));
        $this->assertEquals('1220', $formatter->format(12, 2020));
    }
}
