<?php
namespace Communication\Adapter;

use \Communication\Adapter\AdapterInterface;
use \Communication\Adapter\AdapterAbstract;

use \Communication\Exception\UndefinedHttpMethod;
use \Communication\Exception\CommunicationFailed;

class Soap extends AdapterAbstract implements AdapterInterface
{
    /* @see \Communication\CommunicationInterface::sendRequest() */
    public function sendRequest($url, $data, $options = null)
    {
        throw new CommunicationFailed('Not Implemented Yet!');
    }
}

