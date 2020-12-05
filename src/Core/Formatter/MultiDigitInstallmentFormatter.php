<?php
namespace Paranoia\Core\Formatter;

use Paranoia\Core\Formatter\FormatterInterface;

class MultiDigitInstallmentFormatter implements FormatterInterface
{
    public function format($input)
    {
        return (!is_numeric($input) || intval($input) <= 1) ? '00' : sprintf('%02s', $input);
    }
}
