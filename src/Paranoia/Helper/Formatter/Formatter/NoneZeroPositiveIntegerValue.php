<?php
namespace Paranoia\Helper\Formatter\Formatter;

use Paranoia\Helper\Formatter\FormatterInterface;

class NoneZeroPositiveIntegerValue implements FormatterInterface
{
    public static function format($value)
    {
        return (!is_numeric($value) || intval($value) <= 1) ? '' : $value;
    }
}