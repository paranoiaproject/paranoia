<?php
namespace Paranoia\Builder;

use Paranoia\Configuration\AbstractConfiguration;

abstract class AbstractBuilderFactory
{
    /** @var AbstractConfiguration */
    protected $configuration;

    /**
     * AbstractBuilderFactory constructor.
     * @param AbstractConfiguration $configuration
     */
    public function __construct(AbstractConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param $transactionType
     * @return AbstractRequestBuilder
     */
    abstract protected function createBuilder($transactionType);
}
