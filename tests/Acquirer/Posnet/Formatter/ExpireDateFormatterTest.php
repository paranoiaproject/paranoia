<?php
namespace Paranoia\Test\Acquirer\Posnet\Formatter;

use Paranoia\Acquirer\Posnet\Formatter\ExpireDateFormatter;
use PHPUnit\Framework\TestCase;

class ExpireDateFormatterTest extends TestCase
{
    public function test()
    {
        $formatter = new ExpireDateFormatter();
        $this->assertEquals('9402', $formatter->format([2, 1994]));
    }
}
