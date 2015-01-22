<?php
namespace Paranoia\Communication\Adapter;

use Paranoia\Communication\Exception\UndefinedHttpMethod;
use Paranoia\Communication\Exception\CommunicationFailed;

class Http extends AdapterAbstract implements AdapterInterface
{

    /* Methods */
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    /**
     * @var resource
     */
    private $handler;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->handler = curl_init();
    }

    /**
     * validates http method.
     *
     * @param array $options
     *
     * @return bool
     * @throws \Paranoia\Communication\Exception\UndefinedHttpMethod
     */
    private function validateMethod($options)
    {
        $requestMethods = array(
            self::METHOD_GET,
            self::METHOD_POST,
            self::METHOD_PUT,
            self::METHOD_DELETE
        );
        if (!in_array($options[CURLOPT_CUSTOMREQUEST], $requestMethods)) {
            throw new UndefinedHttpMethod(
                'Undefined http method: ' . $options[CURLOPT_CUSTOMREQUEST]
            );
        }
        return true;
    }

    /**
     * attach data to options.
     *
     * @param string $data
     * @param array  $curlOptions
     *
     * @return array
     */
    private function attachData($data, &$curlOptions)
    {
        $method = $curlOptions[CURLOPT_CUSTOMREQUEST];
        if ($method == self::METHOD_POST || $method == self::METHOD_PUT) {
            $curlOptions[CURLOPT_POSTFIELDS] = $data;
        } else {
            $url             = $curlOptions[CURLOPT_URL];
            $questionmarkPos = strpos($url, '?');
            if ($questionmarkPos !== false) {
                $data .= '&' . substr($url, $questionmarkPos + 1);
                $url = substr($url, 0, $questionmarkPos);
            }
            $url .= '?' . $data;
            $curlOptions[CURLOPT_URL] = $url;
        }
        $this->lastSentRequest = $data;
        return $curlOptions;
    }

    /**
     * sets curl options to handler.
     *
     * @param string $url
     * @param string $data
     * @param array  $options
     *
     * @return array
     */
    private function setOptions($url, $data, $options)
    {
        if (!isset($options['method'])) {
            $options['method'] = self::METHOD_POST;
        }
        $curlOptions = array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER         => false,
            CURLOPT_CUSTOMREQUEST  => $options['method']
        );
        $this->attachData($data, $curlOptions);
        curl_setopt_array($this->handler, $curlOptions);
        return $curlOptions;
    }

    /**
     * {@inheritdoc}
     * @see \Paranoia\Communication\Adapter\AdapterInterface::sendRequest()
     * @throws \Paranoia\Communication\Exception\CommunicationFailed
     */
    public function sendRequest($url, $data, $options = null)
    {
        $curlOptions = $this->setOptions($url, $data, $options);
        $this->validateMethod($curlOptions);
        $response = curl_exec($this->handler);
        $this->lastReceivedResponse = $response;
        $error                      = curl_error($this->handler);
        if ($error) {
            throw  new CommunicationFailed(
                'Communication error occurred. Detail: ' . $error
            );
        }
        return $response;
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
