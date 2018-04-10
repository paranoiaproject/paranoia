<?php
namespace Paranoia\Formatter;

class MultiDigitInstallment implements FormatterInterface
{

    public function format($input)
    {
        return (!is_numeric($input) || intval($input) <= 1) ? '00' : sprintf('%02s', $input);
    }
}
