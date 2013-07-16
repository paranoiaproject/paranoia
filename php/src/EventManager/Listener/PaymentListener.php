<?php
namespace EventManager\Listener;

use \EventManager\Listener\ListenerAbstract;
use \EventManager\EventParameter;

class PaymentListener extends ListenerAbstract
{
   protected function _OnPaymentSuccessful(EventParameter $parameter)
   {

   }

    protected function _OnPaymentFailed(EventParameter $parameter)
    {

    }

    protected function _OnException(EventParameter $parameter)
    {

    }
}
