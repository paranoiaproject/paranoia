<?php
namespace Paranoia\Test\Acquirer\Posnet\Formatter;

use Paranoia\Acquirer\Posnet\Formatter\CustomCurrencyCodeFormatter;
use Paranoia\Core\Constant\Currency;
use Paranoia\Core\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CustomCurrencyCodeFormatterTest extends TestCase
{
    public function test_invalid_input()
    {
        $formatter = new CustomCurrencyCodeFormatter();
        $this->expectException(InvalidArgumentException::class);
        $formatter->format('BAD_CODE');
    }

    public function getCurrencyCodes()
    {
        #TODO: Move this constants to another constant class.
        return [
            [Currency::CODE_EUR, CustomCurrencyCodeFormatter::CODE_EUR],
            [Currency::CODE_USD, CustomCurrencyCodeFormatter::CODE_USD],
            [Currency::CODE_TRY, CustomCurrencyCodeFormatter::CODE_TRY],
        ];
    }

    /**
     * @param $currencyCode
     * @param $expected
     * @dataProvider getCurrencyCodes
     */
    public function test_valid_input($currencyCode, $expected)
    {
        $formatter = new CustomCurrencyCodeFormatter();
        $this->assertEquals($expected, $formatter->format($currencyCode));
    }
}
