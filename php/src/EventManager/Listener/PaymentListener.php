<?php
namespace EventManager\Listener;

use \EventManager\Listener\ListenerAbstract;
use \EventManager\EventParameter;

class PaymentListener extends ListenerAbstract
{
   protected function _OnTransactionFailed(EventParameter $parameter)
   {
   }

   protected function _OnTransactionSuccessful(EventParameter $parameter)
   {
   }

   protected function _OnException(EventParameter $parameter)
   {
   }
}
