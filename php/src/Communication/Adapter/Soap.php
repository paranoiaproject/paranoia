<?php
namespace Communication\Adapter;

use \Communication\Adapter\AdapterInterface;
use \Communication\Exception\UndefinedHttpMethod;
use \Communication\Exception\CommunicationFailed;

class Soap implements AdapterInterface
{
    /* @see \Communication\CommunicationInterface::sendRequest() */
    public function sendRequest($url, $data, $options = null)
    {
        throw new CommunicationFailed('Not Implemented Yet!');
    }
}

