<?php
namespace Paranoia\EventManager;

use Paranoia\EventManager\Listener\ListenerAbstract;

abstract class EventManagerAbstract
{

    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * add listener to listening the specified event.
     *
     * @param string                    $eventName
     * @param Listener\ListenerAbstract $listener
     */
    public function addListener($eventName, ListenerAbstract $listener)
    {
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = array();
        }
        $this->listeners[$eventName][] = $listener;
    }

    /**
     * returns listener collection for the specified eventname.
     *
     * @param string $eventName
     *
     * @return array
     */
    private function getListeners($eventName)
    {
        if (isset($this->listeners[$eventName])) {
            return $this->listeners[$eventName];
        }
        return array();
    }

    /**
     * trigger listener for the specified event.
     *
     * @param string $eventName
     * @param array  $data (optional)
     */
    protected function triggerEvent($eventName, $data = array())
    {
        $parameter = new EventParameter(
            $this,
            $eventName,
            $data,
            microtime(true)
        );

        /* @var $listener \Paranoia\EventManager\Listener\ListenerAbstract */
        foreach ($this->getListeners($eventName) as $listener) {
            $listener->triggerEvent($eventName, $parameter);
        }
    }
}
