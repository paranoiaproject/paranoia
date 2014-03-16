<?php
namespace Paranoia\EventManager;

class EventParameter
{
        /**
     * @var \Paranoia\EventManager\EventManagerAbstract
     */
    private $source;

    /**
     * @var string
     */
    private $eventName;

    /**
     * @var array
     */
    private $data;

    /**
     * @var double
     */
    private $time;

    public function __construct(EventManagerAbstract $source, $eventName, $data = array())
    {
        $this->source    = $source;
        $this->eventName = $eventName;
        $this->data      = $data;
    }

    /**
     * returns event source
     *
     * @return \Paranoia\EventManager\EventManagerAbstract
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * sets event source
     *
     * @param \Paranoia\EventManager\EventManagerAbstract $source
     *
     * @return \Paranoia\EventManager\EventParameter
     */
    public function setSource(EventManagerAbstract $source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * returns event name
     *
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * sets event name
     *
     * @param string $eventName
     *
     * @return \Paranoia\EventManager\EventParameter
     */
    public function setEventName($eventName)
    {
        $this->eventName = $eventName;
        return $this;
    }

    /**
     * returns data.
     *
     * @param string $key
     *
     * @return array
     */
    public function getData($key = null)
    {
        if ($key==null) {
            return $this->data;
        } else {
            return (array_key_exists($key, $this->data)) ?
                $this->data[$key] : false;
        }
    }

    /**
     * sets data.
     *
     * @param array $data
     *
     * @return \Paranoia\EventManager\EventParameter
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * returns event time.
     *
     * @return double
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * sets event time.
     *
     * @param double $time
     *
     * @return \Paranoia\EventManager\EventParameter
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }
}
