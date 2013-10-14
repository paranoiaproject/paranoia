<?php
namespace Pext\Serializer;

use \Pext\Serializer\Adapter\Xml;
use \Pext\Serializer\Adapter\Json;
use \Pext\Serializer\Exception\UnknownSerializer;

class Serializer
{
    const XML = 1;
    const JSON = 2;

    private $_serializer;
    
    /**
     * class constructor.
     *
     * @param int $type
     */
    public function __construct($type)
    {
        switch($type) {
            case self::XML:
                $this->_serializer = new Xml();
                break;
            case self::JSON:
                $this->_serializer = new Json();
                break;
            default:
                throw new UnknownSerializer('Unknown serializer: ' . $type);
        }
    }
    
    /**
     * @see \Pext\Serializer\Adapter\SerializerInterface::serialize()
     */
    public function serialize($data, $options=array())
    {
        return $this->_serializer->serialize($data, $options);
    }
}
