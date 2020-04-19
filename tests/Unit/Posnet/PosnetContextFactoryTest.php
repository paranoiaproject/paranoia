<?php
namespace Paranoia\Test\Unit\Posnet;

use Paranoia\Configuration\PosnetConfiguration;
use Paranoia\Core\ProviderContext;
use Paranoia\Posnet\PosnetContextFactory;
use PHPUnit\Framework\TestCase;

class PosnetContextFactoryTest extends TestCase
{
    public function test_createContext(): void
    {
        $configurationStub = $this->createStub(PosnetConfiguration::class);
        $factory = new PosnetContextFactory($configurationStub);
        $this->assertInstanceOf(ProviderContext::class, $factory->createContext());
    }
}
