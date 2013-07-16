<?php
namespace Communication\Adapter;

use \EventManager\EventManagerAbstract;

abstract class AdapterAbstract extends EventManagerAbstract
{
    /**
     * @return string
     */
    protected $_lastSentRequest;

    /**
     * @return string
     */
    protected $_lastReceivedResponse;
}
