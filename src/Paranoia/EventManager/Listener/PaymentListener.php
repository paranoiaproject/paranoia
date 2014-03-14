<?php
namespace Paranoia\EventManager\Listener;

use Paranoia\EventManager\Listener\ListenerAbstract;
use Paranoia\EventManager\EventParameter;

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
