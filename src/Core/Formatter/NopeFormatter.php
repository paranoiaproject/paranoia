<?php
namespace Paranoia\Core\Formatter;

use Paranoia\Core\Formatter\FormatterInterface;

class NopeFormatter implements FormatterInterface
{
    public function format($input)
    {
        return $input;
    }
}
