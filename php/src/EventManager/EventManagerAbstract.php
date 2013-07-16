<?php
namespace EventManager;

use \EventManager\Listener\ListenerAbstract
use \EventManager\EventParameter;

abstract class EventManagerAbstract
{
    /**
     * @var array
     */
    protected $_listeners = array();

    /**
     * add listener to listening the specified event.
     *
     * @param string $eventName
     * @param \EventManager\Listener\ListenerAbstract
     */
    public function addListener($eventName,  ListenerAbstract $listener)
    {
        if( !isset($this->_listeners[$eventName]) ) {
            $this->_listeners[$eventName] = array();
        }
        $this->_listeners[$eventName][] = $listener;
    }

    /**
     * returns listener collection for the specified eventname.
     *
     * @param string $eventName
     * @return array
     */
    private function _getListeners($eventName)
    {
        if(isset($this->_listeners[$eventName])) {
            return $this->_listeners[$eventName];
        }
        return array();
    }

    /**
     * trigger listener for the specified event.
     *
     * @param EventManagerAbstract $source
     * @param string $eventName,
     * @param array $data (optional)
     */
    protected function _triggerEvent(EventManagerAbstract $source, $eventName,
                                      $data = array())
    {
        $parameter = new EventParameter($source, $eventName,
                                        $data, microtime(true));
        /* @var $listener \EventManager\Listener\ListenerAbstract */
        for($this->_getListeners($eventName) as $listener) {
            $listener->triggerEvent($eventName, $parameter);
        }
    }
}
