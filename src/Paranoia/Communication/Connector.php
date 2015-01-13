<?php
namespace Paranoia\Communication;

use Paranoia\Communication\Exception\UnknownCommunicationAdapter;
use Paranoia\EventManager\Listener\ListenerAbstract;
use Paranoia\Communication\Adapter\Http;
use Paranoia\Communication\Adapter\Soap;

class Connector
{
    private $adapter;

    const CONNECTOR_TYPE_SOAP = 'Soap';
    const CONNECTOR_TYPE_HTTP = 'Http';

    /**
     * determines communication strategy.
     *
     * @param $connectorType
     *
     * @throws UnknownCommunicationAdapter
     */
    public function __construct($connectorType)
    {
        switch ($connectorType) {
            case self::CONNECTOR_TYPE_HTTP:
                $this->adapter = new Http();
                break;
            case self::CONNECTOR_TYPE_SOAP:
                $this->adapter = new Soap();
                break;
            default:
                throw new UnknownCommunicationAdapter(
                    'Unknown communication adapter: ' . $connectorType
                );
        }
    }

    /**
     * @see Paranoia\Communication\Adapter\AdapterInterface::sendRequest()
     */
    public function sendRequest($url, $data, $options = null)
    {
        return $this->adapter->sendRequest($url, $data, $options);
    }

    /**
     * @see Paranoia\EventManager\EventManagerAbstract::addListener()
     */
    public function addListener($eventName, ListenerAbstract $listener)
    {
        return $this->adapter->addListener($eventName, $listener);
    }

    /**
     * @see Paranoia\EventManager\EventManagerAbstract::getLastSentRequest()
     */
    public function getLastSentRequest()
    {
        return $this->adapter->getLastSentRequest();
    }

    /**
     * @see Paranoia\EventManager\EventManagerAbstract::getLastReceivedResponse()
     */
    public function getLastReceivedResponse()
    {
        return $this->adapter->getLastReceivedResponse();
    }
}
