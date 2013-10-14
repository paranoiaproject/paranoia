<?php
namespace Pext\Serializer\Adapter;

interface SerializerInterface
{
    /**
     * returns serialized data.
     * 
     * @param array
     * @return string
     */
    public function serialize($data, $options=array());
}
