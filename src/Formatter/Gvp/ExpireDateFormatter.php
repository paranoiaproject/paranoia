<?php
namespace Paranoia\Formatter\Gvp;

use Paranoia\Formatter\FormatterInterface;

class ExpireDateFormatter implements FormatterInterface
{
    public function format($input)
    {
        return sprintf('%02s%s', $input[0], substr($input[1], -2));
    }
}
