<?php
namespace Paranoia\Processor;

interface ProcessorInterface
{
    /**
     * @param $rawResponse
     * @return \Paranoia\Transfer\Response\ResponseInterface
     */
    public function process($rawResponse);
}