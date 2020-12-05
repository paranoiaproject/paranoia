<?php
namespace Paranoia\Core\Model\Request;

class HttpRequest
{
    public const HTTP_POST = 'POST';

    /** @var string */
    private $url;

    /** @var string */
    private $method;

    /** @var string[] */
    private $headers;

    /** @var string */
    private $body;

    /**
     * HttpRequest constructor.
     * @param string $url
     * @param string $method
     * @param string[] $headers
     * @param string $body
     */
    public function __construct(string $url, string $method, array $headers, string $body)
    {
        $this->url = $url;
        $this->method = $method;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }
}