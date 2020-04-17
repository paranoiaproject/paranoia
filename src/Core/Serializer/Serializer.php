<?php
namespace Paranoia\Core\Serializer;

use Paranoia\Core\Serializer\Adapter\Xml;
// use Paranoia\Common\Serializer\Adapter\Json;
use Paranoia\Core\Exception\InvalidArgumentException;

class Serializer
{

    const XML = 1;
    // const JSON = 2;

    private $serializer;

    /**
     * Serializer constructor.
     * @param $type
     * @throws InvalidArgumentException
     */
    public function __construct($type)
    {
        switch ($type) {
            case self::XML:
                $this->serializer = new Xml();
                break;
            /*
             * JSON Serializer yok
            case self::JSON:
                $this->serializer = new Json();
                break;
            */
            default:
                throw new InvalidArgumentException('Unknown serializer: ' . $type);
        }
    }

    /**
     * @param $data
     * @param array $options
     * @return string
     */
    public function serialize($data, $options = array())
    {
        return $this->serializer->serialize($data, $options);
    }
}
