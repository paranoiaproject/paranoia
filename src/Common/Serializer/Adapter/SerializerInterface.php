<?php
namespace Paranoia\Common\Serializer\Adapter;

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
