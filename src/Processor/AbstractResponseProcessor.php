<?php
namespace Paranoia\Processor;

use Paranoia\Configuration\AbstractConfiguration;

abstract class AbstractResponseProcessor
{
    /** @var AbstractConfiguration */
    protected $configuration;

    /**
     * AbstractResponseProcessor constructor.
     * @param AbstractConfiguration $configuration
     */
    public function __construct(AbstractConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    abstract protected function validateResponse($transformedResponse);

    abstract public function process($rawResponse);
}
