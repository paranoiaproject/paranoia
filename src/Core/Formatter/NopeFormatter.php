<?php
namespace Paranoia\Core\Formatter;

class NopeFormatter implements FormatterInterface
{
    public function format($input)
    {
        return $input;
    }
}
