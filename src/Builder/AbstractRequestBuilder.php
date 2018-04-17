<?php
namespace Paranoia\Builder;

use Paranoia\Configuration\AbstractConfiguration;
use Paranoia\Request;

abstract class AbstractRequestBuilder
{
    /** @var AbstractConfiguration */
    protected $configuration;

    /**
     * AbstractRequestBuilder constructor.
     * @param AbstractConfiguration $configuration
     */
    public function __construct(AbstractConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    abstract public function build(Request $request);
}
