<?php
namespace Paranoia\Acquirer;

use Paranoia\Acquirer\AbstractRequestBuilder;
use Paranoia\Core\AbstractConfiguration;

abstract class AbstractRequestBuilderFactory
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
