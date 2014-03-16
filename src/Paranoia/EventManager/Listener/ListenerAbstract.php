<?php
namespace Paranoia\EventManager\Listener;

use Paranoia\EventManager\EventParameter;
use Paranoia\EventManager\Exception\UndefinedListenerMethod;

abstract class ListenerAbstract
{

    /**
     * trigger the specified event with given parameter.
     *
     * @param string                                $eventName
     * @param \Paranoia\EventManager\EventParameter $parameter
     */
    public function triggerEvent($eventName, EventParameter $parameter)
    {
        $this->validateEvent($eventName);
        $method = $this->getEventMethodName($eventName);
        $this->{$method}($parameter);
    }

    /**
     * returns generic method name for the
     * specified event name.
     *
     * @param string $eventName
     *
     * @return string
     */
    private function getEventMethodName($eventName)
    {
        return ucfirst($eventName);
    }

    /**
     * validates eventname.
     *
     * @param string $eventName
     *
     * @return boolean
     * @throws \Paranoia\EventManager\Exception\UndefinedListenerMethod
     */
    private function validateEvent($eventName)
    {
        $method = $this->getEventMethodName($eventName);
        if (!method_exists($this, $method)) {
            throw new UndefinedListenerMethod(
                'Listener method is not defined for ' . $eventName
            );
        }
        return true;
    }
}
