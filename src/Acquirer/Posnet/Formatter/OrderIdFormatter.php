<?php
namespace Paranoia\Acquirer\Posnet\Formatter;

use Paranoia\Core\Exception\InvalidArgumentException;

class OrderIdFormatter
{
    const MAX_INPUT_LENGTH = 24;

    public function format(string $input): string
    {
        if (strlen($input) > self::MAX_INPUT_LENGTH) {
            throw new InvalidArgumentException('Order ID can not contain more than 24 characters.');
        }

        return str_repeat('0', self::MAX_INPUT_LENGTH - strlen($input)) . $input;
    }
}
