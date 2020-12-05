<?php
namespace Paranoia\Acquirer\Posnet\Formatter;

use Paranoia\Core\Exception\InvalidArgumentException;
use Paranoia\Core\Formatter\FormatterInterface;

class OrderIdFormatter implements FormatterInterface
{
    const MAX_INPUT_LENGTH = 24;

    public function format($input)
    {
        if (strlen($input) > self::MAX_INPUT_LENGTH) {
            throw new InvalidArgumentException('Order ID can not contain more than 24 characters.');
        }

        return str_repeat('0', self::MAX_INPUT_LENGTH - strlen($input)) . $input;
    }
}
