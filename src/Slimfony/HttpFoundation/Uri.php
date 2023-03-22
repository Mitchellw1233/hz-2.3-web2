<?php

namespace Slimfony\HttpFoundation;

class Uri implements UriInterface
{
    protected string $url;
    protected string $scheme;
    protected string $authority;
    protected string $userInfo;
    protected string $host;
    protected ?int $port;
    protected string $path;
    protected string $queryString;
    protected string $fragment;

    public function __construct(string $url)
    {
        $this->url = $url;
        $uri = parse_url($url);

        if ($uri === false) {
            throw new \LogicException('Url is not valid');
        }

        $this->scheme = $parts['scheme'] ?? '';
        $this->userInfo = $parts['user'] ?? '';
        $this->userInfo .= isset($parts['pass']) ? ':' . $parts['pass'] : '';
        $this->host = $parts['host'] ?? '';
        $this->port = $parts['port'] ?? null;
        $this->authority =
            $this->userInfo
            . (!empty($this->userInfo) ? '@' : '')
            . $this->host
            . ($this->port !== null ? ':' : '')
            . $this->port;
        $this->path = $parts['path'] ?? '';
        $this->queryString = $parts['query'] ?? '';
        $this->fragment = $parts['fragment'] ?? '';
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getAuthority(): string
    {
        return $this->authority;
    }

    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int|null
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getQueryString(): string
    {
        return $this->queryString;
    }

    /**
     * @return string
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }
}
