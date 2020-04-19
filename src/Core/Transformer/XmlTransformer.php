<?php
namespace Paranoia\Core\Transformer;

use Paranoia\Core\Exception\InvalidArgumentException;
use SimpleXMLElement;

class XmlTransformer
{
    /**
     * @param string|null $xml
     * @return SimpleXMLElement
     * @throws InvalidArgumentException
     */
    public function transform(?string $xml): SimpleXMLElement
    {
        try {
            return new SimpleXMLElement($xml);
        } catch (\Exception $exception) {
            throw new InvalidArgumentException('Invalid XML content');
        }
    }
}
