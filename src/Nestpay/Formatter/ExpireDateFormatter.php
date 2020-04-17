<?php
namespace Paranoia\Nestpay\Formatter;

class ExpireDateFormatter
{
    public function format(int $expireMonth, int $expireYear): string
    {
        return sprintf('%02s/%04s', $expireMonth, $expireYear);
    }
}
