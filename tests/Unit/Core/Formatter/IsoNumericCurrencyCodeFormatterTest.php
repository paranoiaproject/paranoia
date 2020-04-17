<?php
namespace Paranoia\Test\Unit\Core\Formatter;

use Paranoia\Core\Currency;
use Paranoia\Core\Exception\InvalidArgumentException;
use Paranoia\Core\Formatter\IsoNumericCurrencyCodeFormatter;
use PHPUnit\Framework\TestCase;

class IsoNumericCurrencyCodeFormatterTest extends TestCase
{
    public function test_invalid_input(): void
    {
        $formatter = new IsoNumericCurrencyCodeFormatter();
        $this->expectException(InvalidArgumentException::class);
        $formatter->format('BAD_CODE');
    }

    public function currencyCodeProvider(): array
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
     * @dataProvider currencyCodeProvider
     */
    public function test_valid_input(string $currencyCode, int $expected): void
    {
        $formatter = new IsoNumericCurrencyCodeFormatter();
        $this->assertEquals($expected, $formatter->format($currencyCode));
    }
}
