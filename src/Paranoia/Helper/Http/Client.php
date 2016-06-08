<?php
namespace Paranoia\Helper\Http;

use Guzzle\Http\Client as HttpClient;
use Guzzle\Http\Exception\RequestException;
use Paranoia\Exception\ConnectionError;

class Client
{
    /**
     * @param string $url
     * @param array $data
     * @return \Guzzle\Http\EntityBodyInterface|string
     * @throws ConnectionError
     */
    public static function post($url, array $data)
    {
        $client = new HttpClient();
        $client->setConfig(array(
            'curl.options' => array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            )
        ));
        $request = $client->post($url, null, $data);
        try {
            return $request->send()->getBody();
        } catch (RequestException $e) {
            throw new ConnectionError('Communication failed: ' . $url);
        }
    }
}