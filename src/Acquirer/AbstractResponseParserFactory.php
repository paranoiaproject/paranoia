<?php
namespace Paranoia\Acquirer;

use Paranoia\Core\AbstractConfiguration;

abstract class AbstractResponseParserFactory
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
     * @return AbstractResponseParser
     */
    abstract public function createProcessor($transactionType);
}
