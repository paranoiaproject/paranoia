<?php
namespace Paranoia\Transfer\Request;

class PreAuthorizationRequest extends AbstractRequest
{
    /**
     * @var float
     */
    private $amount;

    /**
     * @var \SebastianBergmann\Money\Currency
     */
    private $currency;

    /**
     * @var \Paranoia\Transfer\Request\Resource\ResourceInterface
     */
    private $resource;

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return PreAuthorizationRequest
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return \SebastianBergmann\Money\Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param \SebastianBergmann\Money\Currency $currency
     * @return PreAuthorizationRequest
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return Resource\ResourceInterface
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param Resource\ResourceInterface $resource
     * @return PreAuthorizationRequest
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }
}