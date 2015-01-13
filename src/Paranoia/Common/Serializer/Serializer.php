<?php
namespace Paranoia\Common\Serializer;

use Paranoia\Common\Serializer\Adapter\Xml;
use Paranoia\Common\Serializer\Adapter\Json;
use Paranoia\Common\Serializer\Exception\UnknownSerializer;

class Serializer
{
    const XML = 1;
    const JSON = 2;

    private $serializer;

    /**
     * class constructor.
     *
     * @param $type
     * @throws Exception\UnknownSerializer
     */
    public function __construct($type)
    {
        switch($type) {
            case self::XML:
                $this->serializer = new Xml();
                break;
            case self::JSON:
                $this->serializer = new Json();
                break;
            default:
                throw new UnknownSerializer('Unknown serializer: ' . $type);
        }
    }
    
    /**
     * @see \Pext\Serializer\Adapter\SerializerInterface::serialize()
     */
    public function serialize($data, $options = array())
    {
        return $this->serializer->serialize($data, $options);
    }
}
