<?php
namespace Paranoia\Gvp\RequestBuilder;

use Paranoia\Configuration\GvpConfiguration;

abstract class BaseRequestBuilder
{
    /** @var GvpConfiguration */
    protected $configuration;

    /**
     * BaseRequestBuilder constructor.
     * @param GvpConfiguration $configuration
     */
    public function __construct(GvpConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    protected function buildSecurityHash(string $password): string
    {
        $tidPrefix  = str_repeat('0', 9 - strlen($this->configuration->getTerminalId()));
        $terminalId = sprintf('%s%s', $tidPrefix, $this->configuration->getTerminalId());
        return strtoupper(SHA1(sprintf('%s%s', $password, $terminalId)));
    }

    protected function buildHash(array $hashContext, string $password): string
    {
        $hashContext[] = $this->buildSecurityHash($password);
        return strtoupper(sha1(implode('', $hashContext)));
    }

    /**
     * @param string $username
     * @param string $hash
     * @return array
     */
    protected function buildTerminal(string $username, string $hash): array
    {
        return [
            'ProvUserID' => $username,
            'HashData' => $hash,
            'UserID' => $username,
            'ID' => $this->configuration->getTerminalId(),
            'MerchantID' => $this->configuration->getMerchantId()
        ];
    }

    /**
     * @param string $orderId
     * @return array
     */
    protected function buildOrder(string $orderId): array
    {
        return [
            'OrderID' => $orderId,
            'GroupID' => null,
            'Description' => null
        ];
    }

    /**
     * @return array
     */
    protected function buildCustomer(): array
    {
        return [
            'IPAddress' => '127.0.0.1',
            'EmailAddress' => 'dummy@dummy.net'
        ];
    }
}
