<?php
namespace Paranoia\Test\Unit\PosnetFormatter\Posnet;

use Paranoia\Posnet\Formatter\ExpireDateFormatter;
use PHPUnit\Framework\TestCase;

class ExpireDateFormatterTest extends TestCase
{
    public function test(): void
    {
        $formatter = new ExpireDateFormatter();
        $this->assertEquals('9402', $formatter->format(2, 1994));
        $this->assertEquals('2012', $formatter->format(12, 2020));
    }
}
