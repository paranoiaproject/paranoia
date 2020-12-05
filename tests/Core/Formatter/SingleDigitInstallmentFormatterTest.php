<?php
namespace Paranoia\Test\Core\Formatter;

use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use PHPUnit\Framework\TestCase;

class SingleDigitInstallmentFormatterTest extends TestCase
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
        $formatter = new SingleDigitInstallmentFormatter();
        $this->assertEquals($expected, $formatter->format($input));
    }
}
