<?php
namespace Paranoia\Acquirer\NestPay\Formatter;

use Paranoia\Core\Formatter\FormatterInterface;

class ExpireDateFormatter implements FormatterInterface
{
    public function format($input)
    {
        return sprintf('%02s/%04s', $input[0], $input[1]);
    }
}
