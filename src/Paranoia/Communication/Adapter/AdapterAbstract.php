<?php
namespace Paranoia\Communication\Adapter;

use Paranoia\EventManager\EventManagerAbstract;

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
