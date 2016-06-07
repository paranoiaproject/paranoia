<?php
namespace Paranoia\Formatter\Formatter;

use Paranoia\Formatter\FormatterInterface;

class NoneZeroPositiveIntegerValue implements FormatterInterface
{
    public static function format($value)
    {
        return (!is_numeric($value) || intval($value) <= 1) ? '' : $value;
    }
}