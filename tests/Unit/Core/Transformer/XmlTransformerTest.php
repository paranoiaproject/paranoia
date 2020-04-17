<?php

namespace Paranoia\Test\Unit\Core\Transformer;

use Paranoia\Core\Exception\InvalidArgumentException;
use Paranoia\Core\Transformer\XmlTransformer;
use PHPUnit\Framework\TestCase;

class XmlTransformerTest extends TestCase
{
    public function invalidInputProvider(): array
    {
        return [
            [null],
            [''],
            ['invalid-input'],
        ];
    }

    public function test_transform_with_valid_input()
    {
        $transformer = new XmlTransformer();
        $content = file_get_contents(__DIR__ . '/../../../stub/core/transformer/valid_data.xml');
        $xml = $transformer->transform($content);
        $this->assertInstanceOf(\SimpleXMLElement::class, $xml);
        $this->assertEquals('SomeValue', $xml->SomeField);
    }

    /**
     * @dataProvider invalidInputProvider
     * @param string|null $content
     */
    public function test_transform_with_invalid_input(?string $content)
    {
        $this->expectException(InvalidArgumentException::class);
        $transformer = new XmlTransformer();
        $transformer->transform($content);
    }
}
