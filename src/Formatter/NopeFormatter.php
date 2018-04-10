<?php
namespace Paranoia\Formatter;

class NopeFormatter implements FormatterInterface
{
    public function format($input)
    {
        return $input;
    }
}
