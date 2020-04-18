<?php
namespace Paranoia\Test\Unit\Gvp;

use Paranoia\Configuration\GvpConfiguration;
use Paranoia\Core\ProviderContext;
use Paranoia\Gvp\GvpContextFactory;
use PHPUnit\Framework\TestCase;

class GvpContextFactoryTest extends TestCase
{
    public function test_createContext(): void
    {
        $configurationStub = $this->createStub(GvpConfiguration::class);
        $factory = new GvpContextFactory($configurationStub);
        $this->assertInstanceOf(ProviderContext::class, $factory->createContext());
    }
}
