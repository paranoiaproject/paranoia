<?php
namespace Paranoia\Core\Formatter;

class SingleDigitInstallmentFormatter
{
    public function format(?int $input): ?string
    {
        return (!is_numeric($input) || intval($input) <= 1) ? null : $input;
    }
}
