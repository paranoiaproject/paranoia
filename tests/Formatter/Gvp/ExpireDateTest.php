<?php
namespace Paranoia\Test\Formatter\Gvp;

use Paranoia\Formatter\Gvp\ExpireDate;
use PHPUnit\Framework\TestCase;

class ExpireDateTest extends TestCase
{
    public function test()
    {
        $formatter = new ExpireDate();
        $this->assertEquals('0294', $formatter->format([2, 1994]));
    }
}