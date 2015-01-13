<?php
namespace Paranoia\EventManager\Listener;

use Paranoia\EventManager\EventParameter;

class PaymentListener extends ListenerAbstract
{
    protected function onTransactionFailed(EventParameter $parameter)
    {
    }

    protected function onTransactionSuccessful(EventParameter $parameter)
    {
    }

    protected function onException(EventParameter $parameter)
    {
    }
}
