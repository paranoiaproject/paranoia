<?php
namespace Paranoia\Test\Formatter\NestPay;

use Paranoia\Formatter\NestPay\ExpireDate;
use PHPUnit\Framework\TestCase;

class ExpireDateTest extends TestCase
{
    public function test()
    {
        $formatter = new ExpireDate();
        $this->assertEquals('02/1994', $formatter->format([2, 1994]));
    }
}