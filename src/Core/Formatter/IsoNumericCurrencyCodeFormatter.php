<?php
namespace Paranoia\Core\Formatter;

use Paranoia\Core\Exception\InvalidArgumentException;
use Paranoia\Core\Currency;

class IsoNumericCurrencyCodeFormatter
{
    const CODE_TRY = 949;
    const CODE_USD = 840;
    const CODE_EUR = 978;

    /**
     * @param string $input
     * @return string
     */
    public function format(string $input): string
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
