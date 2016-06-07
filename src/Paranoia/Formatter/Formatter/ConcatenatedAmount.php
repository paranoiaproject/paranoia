<?php
namespace Paranoia\Formatter\Formatter;

use Paranoia\Formatter\FormatterInterface;

class ConcatenatedAmount implements FormatterInterface
{
    /**
     * @param $value
     * @return mixed
     */
    public static function format($value)
    {
        return number_format($value, 2, '', '');
    }
}