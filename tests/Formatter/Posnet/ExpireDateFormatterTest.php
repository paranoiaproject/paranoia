<?php
namespace Paranoia\Test\Formatter\Posnet;

use Paranoia\Formatter\Posnet\ExpireDateFormatter;
use PHPUnit\Framework\TestCase;

class ExpireDateFormatterTest extends TestCase
{
    public function test()
    {
        $formatter = new ExpireDateFormatter();
        $this->assertEquals('9402', $formatter->format([2, 1994]));
    }
}
