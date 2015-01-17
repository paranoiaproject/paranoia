<?php
namespace Paranoia\EventManager\Listener;

use Paranoia\EventManager\EventParameter;

class CommunicationListener extends ListenerAbstract
{

    protected function beforeRequest(EventParameter $parameter)
    {
        $data = array(
            'Type: Request',
            'Url:' . $parameter->getData('url'),
            'Data: ' . $parameter->getData('data')
        );
        print implode(PHP_EOL, $data) . PHP_EOL;
    }

    protected function afterRequest(EventParameter $parameter)
    {
        $data = array(
            'Type: Response',
            'Url:' . $parameter->getData('url'),
            'Data: ' . $parameter->getData('data')
        );
        print implode(PHP_EOL, $data) . PHP_EOL;
    }

    protected function onException(EventParameter $parameter)
    {
    }
}
