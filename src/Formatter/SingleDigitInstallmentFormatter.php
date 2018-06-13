<?php
namespace Paranoia\Formatter;

class SingleDigitInstallmentFormatter implements FormatterInterface
{
    public function format($input)
    {
        return (!is_numeric($input) || intval($input) <= 1) ? null : $input;
    }
}
