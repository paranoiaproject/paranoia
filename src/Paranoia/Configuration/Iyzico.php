<?php

namespace Paranoia\Configuration;

class Iyzico extends AbstractConfiguration
{

    /**
     * @var string
     */
    private $apiId;

    /**
     * @var string
     */
    private $apiSecret;

    /**
     * @var string
     */
    private $mode;

    /**
     * @param string $apiId
     *
     * @return $this
     */
    public function setApiId($apiId)
    {
        $this->apiId = $apiId;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiId()
    {
        return $this->apiId;
    }

    /**
     * @param string $apiSecret
     *
     * @return $this
     */
    public function setApiSecret($apiSecret)
    {
        $this->apiSecret = $apiSecret;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiSecret()
    {
        return $this->apiSecret;
    }

    /**
     * @param string $mode
     *
     * @return $this
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }
}
