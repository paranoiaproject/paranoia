<?php
namespace Paranoia\Test\Unit\Core\Formatter;

use Paranoia\Core\Formatter\SingleDigitInstallmentFormatter;
use PHPUnit\Framework\TestCase;

class SingleDigitInstallmentFormatterTest extends TestCase
{
    public function expectedValues(): array
    {
        return [
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
    public function test(?string $expected, ?int $input): void
    {
        $formatter = new SingleDigitInstallmentFormatter();
        $this->assertEquals($expected, $formatter->format($input));
    }
}
