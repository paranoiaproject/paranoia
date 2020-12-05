<?php
namespace Paranoia\Acquirer;

use Paranoia\Core\AbstractConfiguration;
use Paranoia\Core\Model\Request;

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
