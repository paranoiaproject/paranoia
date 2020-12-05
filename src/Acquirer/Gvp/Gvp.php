<?php
namespace Paranoia\Acquirer\Gvp;

use Paranoia\Acquirer\AbstractAcquirer;
use Paranoia\Acquirer\Gvp\GvpRequestBuilderFactory;
use Paranoia\Core\AbstractConfiguration;
use Paranoia\Acquirer\Gvp\GvpResponseParserFactory;
use Paranoia\Core\Model\Request;

class Gvp extends AbstractAcquirer
{
    /** @var GvpRequestBuilderFactory */
    private $builderFactory;

    /** @var  GvpResponseParserFactory */
    private $processorFactory;

    public function __construct(AbstractConfiguration $configuration)
    {
        parent::__construct($configuration);
        $this->builderFactory = new GvpRequestBuilderFactory($this->configuration);
        $this->processorFactory = new GvpResponseParserFactory($this->configuration);
    }

    /**
     * {@inheritdoc}
     * @throws \Paranoia\Core\Exception\NotImplementedError
     *@see \Paranoia\Acquirer\AbstractAcquirer::buildRequest()
     */
    protected function buildRequest(Request $request, $transactionType)
    {
        $rawRequest = $this->builderFactory->createBuilder($transactionType)->build($request);
        return array( 'data' => $rawRequest);
    }

    /**
     * {@inheritdoc}
     * @see \Paranoia\Acquirer\AbstractAcquirer::parseResponse()
     */
    protected function parseResponse($rawResponse, $transactionType)
    {
        return $this->processorFactory->createProcessor($transactionType)->process($rawResponse);
    }
}
