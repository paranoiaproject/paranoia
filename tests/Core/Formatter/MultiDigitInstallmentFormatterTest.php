<?php
namespace Paranoia\Test\Core\Formatter;

use Paranoia\Core\Formatter\MultiDigitInstallmentFormatter;
use PHPUnit\Framework\TestCase;

class MultiDigitInstallmentFormatterTest extends TestCase
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
        $formatter = new MultiDigitInstallmentFormatter();
        $this->assertEquals($expected, $formatter->format($input));
    }
}
