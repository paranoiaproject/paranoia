<?php
namespace Paranoia\Pos;

use Paranoia\Builder\AbstractBuilderFactory;
use Paranoia\Builder\NestPayBuilderFactory;
use Paranoia\Configuration\AbstractConfiguration;
use Paranoia\Processor\NestPayProcessorFactory;
use Paranoia\Request;

class NestPay extends AbstractPos
{
    /** @var AbstractBuilderFactory */
    private $builderFactory;

    /** @var NestPayProcessorFactory */
    private $processorFactory;

    public function __construct(AbstractConfiguration $configuration)
    {
        parent::__construct($configuration);
        $this->builderFactory = new NestPayBuilderFactory($this->configuration);
        $this->processorFactory = new NestPayProcessorFactory($this->configuration);
    }


    /**
     * {@inheritdoc}
     * @see \Paranoia\Pos\AbstractPos::buildRequest()
     */
    protected function buildRequest(Request $request, $transactionType)
    {
        $rawRequest = $this->builderFactory->createBuilder($transactionType)->build($request);
        return array( 'DATA' => $rawRequest);
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
