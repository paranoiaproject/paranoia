<?php
namespace Paranoia\Acquirer\NestPay\Formatter;

class ExpireDateFormatter
{
    public function format(array $input): string
    {
        return sprintf('%02s/%04s', $input[0], $input[1]);
    }
}
