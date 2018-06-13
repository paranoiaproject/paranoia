<?php
namespace Paranoia\Formatter;

use Paranoia\Currency;
use Paranoia\Exception\InvalidArgumentException;

class IsoNumericCurrencyCodeFormatter implements FormatterInterface
{
    const CODE_TRY = 949;
    const CODE_USD = 840;
    const CODE_EUR = 978;

    public function format($input)
    {
        switch ($input) {
            case Currency::CODE_EUR:
                return self::CODE_EUR;
            case Currency::CODE_USD:
                return self::CODE_USD;
            case Currency::CODE_TRY:
                return self::CODE_TRY;
            default:
                throw new InvalidArgumentException('Bad currency code: ' . $input);
        }
    }
}
