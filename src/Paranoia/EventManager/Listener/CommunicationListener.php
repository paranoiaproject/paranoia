<?php
namespace Paranoia\EventManager\Listener;

use Paranoia\EventManager\EventParameter;

class CommunicationListener extends ListenerAbstract
{
    protected function beforeRequest(EventParameter $parameter)
    {
        print implode(
            PHP_EOL,
            array(
                'Type: Request',
                'Url:' . $parameter->getData('url'),
                'Data: ' . $parameter->getData('data')
            )
        ) . PHP_EOL;
    }

    protected function afterRequest(EventParameter $parameter)
    {
        print implode(
            PHP_EOL,
            array(
                'Type: Response',
                'Url:' . $parameter->getData('url'),
                'Data: ' . $parameter->getData('data')
            )
        ) . PHP_EOL;
    }

    protected function onException(EventParameter $parameter)
    {
    }
}
