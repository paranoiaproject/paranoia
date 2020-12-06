<?php
namespace Paranoia\Acquirer\Gvp\Formatter;

class ExpireDateFormatter
{
    public function format(array $input): string
    {
        return sprintf('%02s%s', $input[0], substr($input[1], -2));
    }
}
