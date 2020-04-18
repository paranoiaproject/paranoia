<?php
namespace Paranoia\Test\Unit\PosnetFormatter;

use Paranoia\Core\Currency;
use Paranoia\Core\Exception\InvalidArgumentException;
use Paranoia\Posnet\Formatter\CustomCurrencyCodeFormatter;
use PHPUnit\Framework\TestCase;

class CustomCurrencyCodeFormatterTest extends TestCase
{
    public function test_invalid_input(): void
    {
        $formatter = new CustomCurrencyCodeFormatter();
        $this->expectException(InvalidArgumentException::class);
        $formatter->format('BAD_CODE');
    }

    public function currencyProvider(): array
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
     * @dataProvider currencyProvider
     */
    public function test_valid_input($currencyCode, $expected): void
    {
        $formatter = new CustomCurrencyCodeFormatter();
        $this->assertEquals($expected, $formatter->format($currencyCode));
    }
}
