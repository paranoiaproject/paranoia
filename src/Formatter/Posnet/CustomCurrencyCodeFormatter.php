<?php
namespace Paranoia\Formatter\Posnet;

use Paranoia\Currency;
use Paranoia\Exception\InvalidArgumentException;

class CustomCurrencyCodeFormatter
{
    const CODE_TRY = 'YT';
    const CODE_USD = 'US';
    const CODE_EUR = 'EU';

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
