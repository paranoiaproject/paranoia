<?php
namespace Paranoia\Core;

class AbstractConfiguration
{

    /**
     * @var string
     */
    private $apiUrl;

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
}
