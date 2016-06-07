<?php
namespace Paranoia\Helper\Serializer\Serializer;

interface SerializerInterface
{

    /**
     * returns serialized data.
     *
     * @param array $data
     * @param array $options
     *
     * @return string
     */
    public function serialize($data, $options = array());
}
