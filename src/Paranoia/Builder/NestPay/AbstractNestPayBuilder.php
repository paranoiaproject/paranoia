<?php
namespace Paranoia\Builder\NestPay;

use Paranoia\Builder\AbstractBuilder;
use Paranoia\Transfer\Request\RequestInterface;

abstract class AbstractNestPayBuilder extends AbstractBuilder
{
    /**
     * @return array
     */
    protected function prepareCommonParameters()
    {
        /** @var \Paranoia\Configuration\NestPay $config */
        $config = $this->getConfig();

        return array(
            'Name'     => $config->getUsername(),
            'Password' => $config->getPassword(),
            'ClientId' => $config->getClientId(),
            'Mode'     => $config->getMode()
        );
    }
}
