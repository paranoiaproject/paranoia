<?php
namespace Paranoia\Test\Core\Formatter;

use Paranoia\Core\Constant\Currency;
use Paranoia\Core\Exception\InvalidArgumentException;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use PHPUnit\Framework\TestCase;

class IsoNumericCurrencyCodeFormatterTest extends TestCase
{
    public function test_invalid_input()
    {
        $formatter = new IsoNumericCurrencyCodeFormatter();
        $this->expectException(InvalidArgumentException::class);
        $formatter->format('BAD_CODE');
    }

    public function getCurrencyCodes()
    {
        #TODO: Move this constants to another constant class.
        return [
            [Currency::CODE_EUR, IsoNumericCurrencyCodeFormatter::CODE_EUR],
            [Currency::CODE_USD, IsoNumericCurrencyCodeFormatter::CODE_USD],
            [Currency::CODE_TRY, IsoNumericCurrencyCodeFormatter::CODE_TRY],
        ];
    }

    /**
     * @param $currencyCode
     * @param $expected
     * @dataProvider getCurrencyCodes
     */
    public function test_valid_input($currencyCode, $expected)
    {
        $formatter = new IsoNumericCurrencyCodeFormatter();
        $this->assertEquals($expected, $formatter->format($currencyCode));
    }
}
