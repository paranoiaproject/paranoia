<?php
namespace Paranoia\Processor;

use Paranoia\Configuration\AbstractConfiguration;

abstract class AbstractProcessorFactory
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
     * @param string $transactionType
     * @return AbstractResponseProcessor
     */
    abstract public function createProcessor($transactionType);
}
