<?php
namespace Paranoia\Core\Transformer;

use Paranoia\Core\Exception\InvalidArgumentException;
use SimpleXMLElement;

class XmlTransformer
{
    public function transform(?string $xml): SimpleXMLElement
    {
        try {
            return new SimpleXMLElement($xml);
        } catch (\Exception $exception) {
            throw new InvalidArgumentException('Invalid XML content');
        }
    }
}
