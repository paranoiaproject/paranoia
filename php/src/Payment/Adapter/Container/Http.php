<?php
namespace Payment\Adapter\Container;

use \Payment\Adapter\AdapterAbstract;
use \Payment\Adapter\Container\Exceptions\UnknownRequestMethod;
use \Payment\Adapter\Container\Exceptions\ConnectionError;

abstract class Http  extends AdapterAbstract
{
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    
    /**
    * @param string $method
    * @param string $data
    * @param array &$options
    */
    private function _attachData($method, $data, &$options)
    {
        $options[CURLOPT_CUSTOMREQUEST] = $method;
        $sendInBody = ( $method == Http::METHOD_POST || 
                        $method == Http::METHOD_PUT) ?
                            true : false;
        if($sendInBody) {
            $options[CURLOPT_POSTFIELDS] = $data;
        } else {
            $url = $options[CURLOPT_URL];
            $questionmarkPos = strpos($url, '?');
            if( $questionmarkPos !== false) {
                $data .= '&' . substr($url, $questionmarkPos+1);
                $url = substr($url, 0, $questionmarkPos);
            }
            $url .= '?' . $data;
            $options[CURLOPT_URL] = $url;
        }
    }
    
    /**
    * @param resource $ch
    * @param string $url
    * @param string $rawRequest
    * @param string $options
    * @return array
    */
    private function _setup($ch, $url, $rawRequest, $option )
    {
        $method = ( isset( $options['method'] ) ) ? 
                  $options['method'] :  Http::METHOD_POST;
        if( ! in_array($method, array(Http::METHOD_POST, 
                                    Http::METHOD_GET, 
                                    Http::METHOD_PUT, 
                                    Http::METHOD_DELETE))) {
            throw new UnknownRequestMethod($method . ' is unknown request ' .
                                          'method.');
        }
        $options = array(CURLOPT_URL => $url,
                         CURLOPT_RETURNTRANSFER => true,
                         CURLOPT_SSL_VERIFYPEER => false,
                         CURLOPT_SSL_VERIFYHOST => true, 
                         CURLOPT_HEADER => false);
       $this->_attachData($method, $rawRequest, $options);
       curl_setopt_array($ch, $options);
    }

    /**
    * @see \Payment\Container\ContainerInterface
    */
    protected function _sendRequest($url, $rawRequest, $options = array())
    {   
        $ch = curl_init();
        $this->_setup($ch, $url, $rawRequest, $options);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        if($error) {
            throw new ConnectionError('Failed connection to ' . $url);
        }
        curl_close($ch);
        return $response;
    }
}
