<?php
namespace Paranoia\Communication\Adapter;

use Paranoia\Communication\Adapter\AdapterInterface;
use Paranoia\Communication\Adapter\AdapterAbstract;
use Paranoia\Communication\Exception\UndefinedHttpMethod;
use Paranoia\Communication\Exception\CommunicationFailed;

class Soap extends AdapterAbstract implements AdapterInterface
{

    /**
     *
     * @see \Communication\CommunicationInterface::sendRequest()
     */
    public function sendRequest($url, $data, $options = null)
    {
        throw new CommunicationFailed('Not Implemented Yet!');
    }

    /**
     * returns last sent request.
     *
     * @return string
     */
    public function getLastSentRequest()
    {
        return $this->lastSentRequest;
    }

    /**
     * returns last received response from provider.
     *
     * @return string
     */
    public function getLastReceivedResponse()
    {
        return $this->lastReceivedResponse;
    }
}
