<?php
namespace Paranoia\Test\Formatter;

use Paranoia\Formatter\MultiDigitInstallment;
use PHPUnit\Framework\TestCase;

class MultiDigitInstallmentTest extends TestCase
{
    public function expectedValues()
    {
        return [
            ['00', 'bad input'],
            ['00', null],
            ['00', 0],
            ['00', -1],
            ['00', 1],
            ['02', 2],
        ];
    }

    /**
     * @dataProvider expectedValues
     * @param $expected
     * @param $input
     */
    public function test_valid_input($expected, $input)
    {
        $formatter = new MultiDigitInstallment();
        $this->assertEquals($expected, $formatter->format($input));
    }
}
