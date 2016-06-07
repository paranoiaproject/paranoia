<?php
namespace Paranoia\Builder;

use Paranoia\Configuration\ConfigurationInterface;

abstract class AbstractBuilder implements BuilderInterface
{
    /**
     * @var \Paranoia\Configuration\ConfigurationInterface
     */
    private $config;

    public function __construct(ConfigurationInterface $config)
    {
        $this->config = $config;
    }

    protected function getConfig()
    {
        return $this->config;
    }
}
