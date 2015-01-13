<?php
namespace Paranoia\Communication\Adapter;

use Paranoia\EventManager\EventManagerAbstract;

abstract class AdapterAbstract extends EventManagerAbstract
{

    /**
     * @return string
     */
    protected $lastSentRequest;

    /**
     * @return string
     */
    protected $lastReceivedResponse;
}
