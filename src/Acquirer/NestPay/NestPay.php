<?php
namespace Paranoia\Acquirer\NestPay;

use Paranoia\Acquirer\AbstractAcquirer;
use Paranoia\Acquirer\AbstractRequestBuilderFactory;
use Paranoia\Acquirer\NestPay\NestPayRequestBuilderFactory;
use Paranoia\Core\AbstractConfiguration;
use Paranoia\Acquirer\NestPay\NestPayResponseParserFactory;
use Paranoia\Core\Model\Request;

class NestPay extends AbstractAcquirer
{
    /** @var AbstractRequestBuilderFactory */
    private $builderFactory;

    /** @var NestPayResponseParserFactory */
    private $processorFactory;

    public function __construct(AbstractConfiguration $configuration)
    {
        parent::__construct($configuration);
        $this->builderFactory = new NestPayRequestBuilderFactory($this->configuration);
        $this->processorFactory = new NestPayResponseParserFactory($this->configuration);
    }


    /**
     * {@inheritdoc}
     * @throws \Paranoia\Core\Exception\NotImplementedError
     *@see \Paranoia\Acquirer\AbstractAcquirer::buildRequest()
     */
    protected function buildRequest(Request $request, $transactionType)
    {
        $rawRequest = $this->builderFactory->createBuilder($transactionType)->build($request);
        return array( 'DATA' => $rawRequest);
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
