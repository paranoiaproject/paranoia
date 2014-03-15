<?php
namespace Paranoia\Communication\Adapter;

use Paranoia\Communication\Exception\CommunicationFailed;

class Soap extends AdapterAbstract implements AdapterInterface
{

    /**
     * @see \Paranoia\Communication\Adapter\AdapterInterface::sendRequest()
     * @throws \Paranoia\Communication\Exception\CommunicationFailed
     */
    public function sendRequest( $url, $data, $options = null )
    {
        throw new CommunicationFailed( 'Not Implemented Yet!' );
    }

    /**
     * returns last sent request.
     *
     * @return string
     */
    public function getLastSentRequest()
    {
        return $this->_lastSentRequest;
    }

    /**
     * returns last received response from provider.
     *
     * @return string
     */
    public function getLastReceivedResponse()
    {
        return $this->_lastReceivedResponse;
    }
}

