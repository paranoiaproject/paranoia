<?php
namespace Paranoia\Gvp\Formatter;

class ExpireDateFormatter
{
    /**
     * @param int $expireMonth
     * @param int $expireYear
     * @return string
     */
    public function format(int $expireMonth, int $expireYear): string
    {
        return sprintf('%02s%s', $expireMonth, substr($expireYear, -2));
    }
}
