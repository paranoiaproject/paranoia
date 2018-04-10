<?php
namespace Paranoia\Test\Formatter;

use Paranoia\Currency;
use Paranoia\Exception\InvalidArgumentException;
use Paranoia\Formatter\IsoNumericCurrencyCode;
use PHPUnit\Framework\TestCase;

class IsoNumericCurrencyCodeTest extends TestCase
{
    public function test_invalid_input()
    {
        $formatter = new IsoNumericCurrencyCode();
        $this->expectException(InvalidArgumentException::class);
        $formatter->format('BAD_CODE');
    }

    public function getCurrencyCodes()
    {
        #TODO: Move this constants to another constant class.
        return [
            [Currency::CODE_EUR, IsoNumericCurrencyCode::CODE_EUR],
            [Currency::CODE_USD, IsoNumericCurrencyCode::CODE_USD],
            [Currency::CODE_TRY, IsoNumericCurrencyCode::CODE_TRY],
        ];
    }

    /**
     * @param $currencyCode
     * @param $expected
     * @dataProvider getCurrencyCodes
     */
    public function test_valid_input($currencyCode, $expected)
    {
        $formatter = new IsoNumericCurrencyCode();
        $this->assertEquals($expected, $formatter->format($currencyCode));
    }
}
