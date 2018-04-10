<?php
namespace Paranoia\Test\Formatter;

use Paranoia\Currency;
use Paranoia\Exception\InvalidArgumentException;
use Paranoia\Formatter\PosnetCurrencyCode;
use PHPUnit\Framework\TestCase;

class PosnetCurrencyCodeTest extends TestCase
{
    public function test_invalid_input()
    {
        $formatter = new PosnetCurrencyCode();
        $this->expectException(InvalidArgumentException::class);
        $formatter->format('BAD_CODE');
    }

    public function getCurrencyCodes()
    {
        #TODO: Move this constants to another constant class.
        return [
            [Currency::CODE_EUR, PosnetCurrencyCode::CODE_EUR],
            [Currency::CODE_USD, PosnetCurrencyCode::CODE_USD],
            [Currency::CODE_TRY, PosnetCurrencyCode::CODE_TRY],
        ];
    }

    /**
     * @param $currencyCode
     * @param $expected
     * @dataProvider getCurrencyCodes
     */
    public function test_valid_input($currencyCode, $expected)
    {
        $formatter = new PosnetCurrencyCode();
        $this->assertEquals($expected, $formatter->format($currencyCode));
    }
}
