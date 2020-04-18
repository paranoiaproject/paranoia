<?php
namespace Paranoia\Posnet\Formatter;

class ExpireDateFormatter
{
    public function format(int $expireMonth, int $expireYear): string
    {
        return sprintf('%02s%02s', substr((string) $expireYear, -2), $expireMonth);
    }
}
