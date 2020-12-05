<?php
namespace Paranoia\Acquirer\Posnet;

use Paranoia\Acquirer\AbstractAcquirer;
use Paranoia\Core\AbstractConfiguration;
use Paranoia\Core\Model\Request;

class Posnet extends AbstractAcquirer
{
    /** @var PosnetRequestBuilderFactory */
    private $builderFactory;

    /** @var PosnetResponseParserFactory */
    private $processorFactory;

    public function __construct(AbstractConfiguration $configuration)
    {
        parent::__construct($configuration);
        $this->builderFactory = new PosnetRequestBuilderFactory($this->configuration);
        $this->processorFactory = new PosnetResponseParserFactory($this->configuration);
    }

    /**
     * {@inheritdoc}
     * @throws \Paranoia\Core\Exception\NotImplementedError
     *@see \Paranoia\Acquirer\AbstractAcquirer::buildRequest()
     */
    protected function buildRequest(Request $request, $transactionType)
    {
        $rawRequest = $this->builderFactory->createBuilder($transactionType)->build($request);
        return array( 'xmldata' => $rawRequest);
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
