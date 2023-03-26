<?php

namespace Slimfony\HttpFoundation;

class Cookie
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $value;

    /**
     * @var int
     */
    protected int $expires;

    /**
     * @var string
     */
    protected string $path;

    /**
     * @var string
     */
    protected string $domain;

    /**
     * @var bool
     */
    protected bool $secure;

    /**
     * @var bool
     */
    protected bool $httpOnly;

    /**
     * @param string $name
     * @param string $value
     * @param int $expires
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httpOnly
     */
    public function __construct(string $name, string $value="", int $expires = 0, string $path="", string $domain="", bool $secure=false, bool $httpOnly=false)
    {
        $this->name = $name;
        $this->value = $value;
        $this->expires = $expires;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Cookie
     */
    public function setName(string $name): Cookie
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return Cookie
     */
    public function setValue(string $value): Cookie
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpires(): int
    {
        return $this->expires;
    }

    /**
     * @param int $expires
     * @return Cookie
     */
    public function setExpires(int $expires): Cookie
    {
        $this->expires = $expires;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return Cookie
     */
    public function setPath(string $path): Cookie
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     * @return Cookie
     */
    public function setDomain(string $domain): Cookie
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * @param bool $secure
     * @return Cookie
     */
    public function setSecure(bool $secure): Cookie
    {
        $this->secure = $secure;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHttpOnly(): bool
    {
        return $this->httpOnly;
    }

    /**
     * @param bool $httpOnly
     * @return Cookie
     */
    public function setHttpOnly(bool $httpOnly): Cookie
    {
        $this->httpOnly = $httpOnly;
        return $this;
    }
}