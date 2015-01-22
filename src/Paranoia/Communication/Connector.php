<?php
namespace Paranoia\Communication;

use Paranoia\Communication\Exception\UnknownCommunicationAdapter;
use Paranoia\Communication\Adapter\Http;
use Paranoia\Communication\Adapter\Soap;

class Connector
{

    const CONNECTOR_TYPE_SOAP = 'Soap';
    const CONNECTOR_TYPE_HTTP = 'Http';

    private $adapter;

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
     * {@inheritdoc}
     * @see \Paranoia\Communication\Adapter\AdapterInterface::sendRequest()
     */
    public function sendRequest($url, $data, $options = null)
    {
        return $this->adapter->sendRequest($url, $data, $options);
    }

    /**
     * {@inheritdoc}
     * @see \Paranoia\Communication\Adapter\AdapterInterface::getLastSentRequest()
     */
    public function getLastSentRequest()
    {
        return $this->adapter->getLastSentRequest();
    }

    /**
     * {@inheritdoc}
     * @see \Paranoia\Communication\Adapter\AdapterInterface::getLastReceivedResponse()
     */
    public function getLastReceivedResponse()
    {
        return $this->adapter->getLastReceivedResponse();
    }
}
