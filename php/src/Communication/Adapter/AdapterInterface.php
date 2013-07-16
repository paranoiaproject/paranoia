<?php
namespace Communication\Adapter;

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
}
