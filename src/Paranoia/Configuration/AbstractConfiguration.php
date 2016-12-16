<?php
namespace Paranoia\Configuration;

class AbstractConfiguration
{

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var string
     */
    private $api3DUrl;

    /**
     * @var string
     */
    private $successUrl;

    /**
     * @var string
     */
    private $errorUrl;

    /**
     * @param string $apiUrl
     *
     * @return $this
     */
    public function setApiUrl($apiUrl)
    {
        $this->apiUrl = $apiUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * @param string $api3DUrl
     *
     * @return $this
     */
    public function setApi3DUrl($api3DUrl)
    {
        $this->api3DUrl = $api3DUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getApi3DUrl()
    {
        return $this->api3DUrl;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setSuccessUrl($url)
    {
        $this->successUrl = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getSuccessUrl()
    {
        return $this->successUrl;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setErrorUrl($url)
    {
        $this->errorUrl = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getErrorUrl()
    {
        return $this->errorUrl;
    }
}
