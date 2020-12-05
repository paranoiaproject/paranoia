<?php
namespace Paranoia\Core\Formatter;

use Paranoia\Core\Formatter\FormatterInterface;

class SingleDigitInstallmentFormatter implements FormatterInterface
{
    public function format($input)
    {
        return (!is_numeric($input) || intval($input) <= 1) ? null : $input;
    }
}
