<?php
namespace Paranoia\Core\Formatter;

use Paranoia\Core\Exception\InvalidArgumentException;

class DecimalFormatter
{
    /**
     * @param float $input
     * @return string
     */
    public function format(?float $input): string
    {
        if (!is_numeric($input)) {
            throw new InvalidArgumentException('The input value must be numeric.');
        }

        return number_format($input, 2, '.', '');
    }
}
