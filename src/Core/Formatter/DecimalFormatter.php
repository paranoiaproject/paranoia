<?php
namespace Paranoia\Core\Formatter;

use Paranoia\Core\Exception\InvalidArgumentException;

class DecimalFormatter
{
    public function format(float $input): string
    {
        if (!is_numeric($input)) {
            throw new InvalidArgumentException('The input value must be numeric.');
        }

        return number_format($input, 2, '.', '');
    }
}
