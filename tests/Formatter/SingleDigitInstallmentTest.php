<?php
namespace Paranoia\Test\Formatter;

use Paranoia\Formatter\SingleDigitInstallment;
use PHPUnit\Framework\TestCase;

class SingleDigitInstallmentTest extends TestCase
{
    public function expectedValues()
    {
        return [
            [null, 'bad input'],
            [null, null],
            [null, 0],
            [null, -1],
            [null, 1],
            ['2', 2],
        ];
    }

    /**
     * @dataProvider expectedValues
     * @param $expected
     * @param $input
     */
    public function test($expected, $input)
    {
        $formatter = new SingleDigitInstallment();
        $this->assertEquals($expected, $formatter->format($input));
    }
}
