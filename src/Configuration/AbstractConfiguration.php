<?php
namespace Paranoia\Configuration;

class AbstractConfiguration
{

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var array|null
     */
    private $guzzleConfig;

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
     * @return null|array
     */
    public function getGuzzleConfig()
    {
        return $this->guzzleConfig;
    }

    /**
     * @param null|array $guzzleConfig
     */
    public function setGuzzleConfig($guzzleConfig)
    {
        $this->guzzleConfig = $guzzleConfig;
    }

}
