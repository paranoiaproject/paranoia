<?php
namespace Paranoia\Helper\Serializer;

use Paranoia\Helper\Serializer\Serializer\SerializerInterface;

class Serializer
{
    /**
     * @param SerializerInterface $serializer
     * @param array $data
     * @param array $options
     * @return string
     */
    public static function serialize(SerializerInterface $serializer, $data, $options = array())
    {
        return $serializer->serialize($data, $options);
    }
}
