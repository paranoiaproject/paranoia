<?php
namespace Paranoia\Configuration;

class Posnet extends AbstractConfiguration
{
    /**
     * @var int
     */
    private $merchantId;

    /**
     * @var int
     */
    private $terminalId;

    /**
     * @param int $merchantId
     *
     * @return $this
     */
    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;
        return $this;
    }

    /**
     * @return int
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * @param int $terminalId
     *
     * @return $this
     */
    public function setTerminalId($terminalId)
    {
        $this->terminalId = $terminalId;
        return $this;
    }

    /**
     * @return int
     */
    public function getTerminalId()
    {
        return $this->terminalId;
    }
}
