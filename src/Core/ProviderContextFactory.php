<?php
namespace Paranoia\Core;

interface ProviderContextFactory
{
    /**
     * @return ProviderContext
     */
    public function createContext(): ProviderContext;
}
