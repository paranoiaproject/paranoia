<?php
namespace Communication;

use \Communication\Exception\UnknownCommunicationAdapter;

use \EventManager\Listener\ListenerAbstract;

class Connector
{
    private $_adapter;

    const CONNECTOR_TYPE_SOAP = 'Soap';
    const CONNECTOR_TYPE_HTTP = 'Http';

    /**
     * determines communication strategy.
     *
     * @param string $connectorType
     */
    public function __construct($connectorType)
    {
        switch($connectorType) {
            case self::CONNECTOR_TYPE_HTTP:
                $this->_adapter = new \Communication\Adapter\Http();
                break;
            case self::CONNECTOR_TYPE_SOAP:
                $this->_adapter = new \Communication\Adapter\Soap();
                break;
            default:
                throw new UnknownCommunicationAdapter('Unknown communication ' .
                                                     'adapter: ' .
                                                     $connectorType);
        }
    }

    /*
     * @see \Communication\CommunicationInterface::sendRequest()
     */
    public function sendRequest($url, $data, $options=null)
    {
        return $this->_adapter->sendRequest($url, $data, $options);
    }

    /**
     * @see \EventManager\EventManagerAbstract::addListener()
     */
    public function addListener($eventName,  ListenerAbstract $listener)
    {
        return $this->_adapter->addListener($eventName, $listener);
    }

    /**
     * @see \EventManager\EventManagerAbstract::getLastSentRequest()
     */
    public function getLastSentRequest()
    {
        return $this->_adapter->getLastSentRequest();
    }

    /**
     * @see \EventManager\EventManagerAbstract::getLastReceivedResponse()
     */
    public function getLastReceivedResponse()
    {
        return $this->_adapter->getLastReceivedResponse();
    }
}
