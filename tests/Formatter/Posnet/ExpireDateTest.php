<?php
namespace Paranoia\Test\Formatter\Posnet;

use Paranoia\Formatter\Posnet\ExpireDate;
use PHPUnit\Framework\TestCase;

class ExpireDateTest extends TestCase
{
    public function test()
    {
        $formatter = new ExpireDate();
        $this->assertEquals('9402', $formatter->format([2, 1994]));
    }
}
