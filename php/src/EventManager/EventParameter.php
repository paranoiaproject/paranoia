<?php
namespace EventManager;

use \EventManager\EventManagerAbstract;

class EventParameter
{
    /**
     * @var \EventManager\EventManagerAbstract
     */
    private $_source;

    /**
     * @var string
     */
    private $_eventName;

    /**
     * @var array
     */
    private $_data;

    public function __construct(EventManagerAbstract $source, $eventName, $data = array())
    {
        $this->_source = $source;
        $this->_eventName = $eventName;
        $this->_data = $data;
    }

    /**
     * returns event source
     *
     * @return \EventManager\EventManagerAbstract
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * sets event source
     *
     * @param \EventManager\EventManagerAbstract $source
     * @return \EventManager\EventParameter
     */
    public function setSource(EventManagerAbstract $source)
    {
        $this->_source = $source;
        return $this;
    }

    /**
     * returns event name
     *
     * @return string
     */
    public function getEventName()
    {
        return $this->_eventName;
    }

    /**
     * sets event name
     *
     * @param string $eventName
     * @return \EventManager\EventParameter
     */
    public function setEventName($eventName)
    {
        $this->_eventName = $eventName;
        return $this;
    }

    /**
     * returns data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * sets data.
     *
     * @param array $data
     * @return \EventManager\EventParameter
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }
}
