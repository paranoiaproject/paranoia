<?php
namespace Paranoia\Acquirer\Posnet\Formatter;

class ExpireDateFormatter
{
    public function format(array $input): string
    {
        return sprintf('%02s%02s', substr($input[1], -2), $input[0]);
    }
}
