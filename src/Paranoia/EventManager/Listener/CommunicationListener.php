<?php
namespace Paranoia\EventManager\Listener;

use Paranoia\EventManager\Listener\ListenerAbstract;
use Paranoia\EventManager\EventParameter;

class CommunicationListener extends ListenerAbstract
{
    protected function _BeforeRequest(EventParameter $parameter)
    {
        print implode(PHP_EOL, array(
            'Type: Request',
            'Url:' . $parameter->getData('url'),
            'Data: ' . $parameter->getData('data')
        )) . PHP_EOL;
    }

    protected function _AfterRequest(EventParameter $parameter)
    {
        print implode(PHP_EOL, array(
            'Type: Response' ,
            'Url:' . $parameter->getData('url'),
            'Data: ' . $parameter->getData('data')
        )) . PHP_EOL;
    }

    protected function _OnException(EventParameter $parameter)
    {

    }
}
