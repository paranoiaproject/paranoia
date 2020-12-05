<?php
namespace Paranoia\Acquirer;

use Paranoia\Core\AbstractConfiguration;

abstract class AbstractResponseParser
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

    abstract public function parse($rawResponse);
}
