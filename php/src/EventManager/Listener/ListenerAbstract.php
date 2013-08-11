<?php
namespace EventManager\Listener;

use \EventManager\EventParameter;
use \EventManager\Exception\UndefinedListenerMethod;
abstract class ListenerAbstract
{
    /**
     * trigger the specified event with given parameter.
     *
     * @param string $eventName
     * @param \EventManager\EventParameter $parameter
     */
    public function triggerEvent($eventName, EventParameter $parameter)
    {
        $this->_validateEvent($eventName);
        $method = $this->_getEventMethodName($eventName);
        $this->{$method}($parameter);
    }

    /**
     * returns generic method name for the
     * specified event name.
     *
     * @param string $eventName
     * @return string
     */
    private function _getEventMethodName($eventName)
    {
        return sprintf('_%s', ucfirst($eventName));
    }

    /**
     * validates eventname.
     *
     * @param string $eventName
     * @return boolean
     * @throws \EventManager\Exception\UndefinedListenerMethod
     */
    private function _validateEvent($eventName)
    {
        $method = $this->_getEventMethodName($eventName);
        if(!method_exists($this, $method)) {
            throw new UndefinedListenerMethod('Listener method is ' .
                                              'not defined for ' . $eventName);

        }
        return true;
    }
}
