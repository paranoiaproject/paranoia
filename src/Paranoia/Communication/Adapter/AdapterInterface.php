<?php
namespace Paranoia\Communication\Adapter;

interface AdapterInterface
{
    /**
     * send a request with given data.
     *
     * @param string $url
     * @param mixed $data
     * @param array $options (optional)
     */
    public function sendRequest($url, $data, $options=null);

    /**
     * returns last sent request.
     *
     * @return string
     */
    public function getLastSentRequest();

    /**
     * returns last received response from provider.
     *
     * @return string
     */
    public function getLastReceivedResponse();
}
