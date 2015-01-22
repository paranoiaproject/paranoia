<?php
namespace Paranoia\Communication\Adapter;

abstract class AdapterAbstract
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
