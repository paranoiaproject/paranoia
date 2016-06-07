<?php
namespace Paranoia\Builder;

use Paranoia\Transfer\Request\RequestInterface;

interface BuilderInterface
{
    /**
     * @param RequestInterface $request
     * @return mixed
     */
    public function build(RequestInterface $request);
}