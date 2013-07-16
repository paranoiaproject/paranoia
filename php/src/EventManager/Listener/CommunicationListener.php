<?php
namespace EventManager\Listener;

use \EventManager\Listener\ListenerAbstract;
use \EventManager\EventParameter;

class CommunicationListener extends ListenerAbstract
{
    protected function _BeforeRequest(EventParameter $parameter)
    {
        print implode(PHP_EOL, array(
            'Type: Request' ,
            'Data: ' . $parameter->getData('data')
        )) . PHP_EOL;
    }

    protected function _AfterRequest(EventParameter $parameter)
    {
        print implode(PHP_EOL, array(
            'Type: Response' ,
            'Data: ' . $parameter->getData('data')
        )) . PHP_EOL;
    }

    protected function _OnException(EventParameter $parameter)
    {

    }
}
