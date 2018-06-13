<?php
namespace Paranoia\Formatter\Posnet;

use Paranoia\Formatter\FormatterInterface;

class ExpireDateFormatter implements FormatterInterface
{
    public function format($input)
    {
        return sprintf('%02s%02s', substr($input[1], -2), $input[0]);
    }
}
