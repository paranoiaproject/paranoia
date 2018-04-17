<?php
namespace Paranoia\Pos;

use Paranoia\Builder\GvpBuilderFactory;
use Paranoia\Configuration\AbstractConfiguration;
use Paranoia\Processor\GvpProcessorFactory;
use Paranoia\Request;

class Gvp extends AbstractPos
{
    /** @var GvpBuilderFactory */
    private $builderFactory;

    /** @var  GvpProcessorFactory */
    private $processorFactory;

    public function __construct(AbstractConfiguration $configuration)
    {
        parent::__construct($configuration);
        $this->builderFactory = new GvpBuilderFactory($this->configuration);
        $this->processorFactory = new GvpProcessorFactory($this->configuration);
    }

    /**
     * {@inheritdoc}
     * @see \Paranoia\Pos\AbstractPos::buildRequest()
     */
    protected function buildRequest(Request $request, $transactionType)
    {
        $rawRequest = $this->builderFactory->createBuilder($transactionType)->build($request);
        return array( 'data' => $rawRequest);
    }

    /**
     * {@inheritdoc}
     * @see \Paranoia\Pos\AbstractPos::parseResponse()
     */
    protected function parseResponse($rawResponse, $transactionType)
    {
        return $this->processorFactory->createProcessor($transactionType)->process($rawResponse);
    }
}
