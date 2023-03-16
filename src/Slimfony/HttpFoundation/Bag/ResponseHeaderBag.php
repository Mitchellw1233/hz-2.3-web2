<?php

namespace Slimfony\HttpFoundation\Bag;

use Slimfony\HttpFoundation\Cookie;

class ResponseHeaderBag extends HeaderBag
{
    public const COOKIES_FLAT = 'flat';
    public const COOKIES_ARRAY = 'array';

    protected array $cookies = [];
    protected array $headerNames = [];
    public function __construct(array $headers = [])
    {
        parent::__construct($headers);

        if (!isset($this->data['cache-control'])) {
            $this->set('cache-control', '');
        }

        if (!isset($this->data['headers'])) {
            $this->set('Date', gmdate('D, d M Y H:i:s').' GMT');
        }
    }

    public function allWithoutCookies(): array
    {
        $headers = $this->all();
        if (isset($this->headerNames['set-cookie'])) {
            unset($headers[$this->headerNames['set-cookie']]);
        }

        return $headers;
    }

    public function setCookie(Cookie $cookie)
    {
        $this->cookies[$cookie->getDomain()][$cookie->getPath()][$cookie->getName()] = $cookie;
        $this->headerNames['set-cookie'] = 'Set-Cookie';
    }

    public function getCookies(string $format = self::COOKIES_FLAT): array
    {
        if (!\in_array($format, [self::COOKIES_FLAT, self::COOKIES_ARRAY])) {
            throw new \InvalidArgumentException(sprintf('Format "%s" invalid (%s).', $format, implode(', ', [self::COOKIES_FLAT, self::COOKIES_ARRAY])));
        }

        if (self::COOKIES_FLAT === $format) {
            return $this->cookies;
        }

        $flattenedCookies = [];
        foreach ($this->cookies as $path) {
            foreach ($path as $cookies) {
                foreach ($cookies as $cookie) {
                    $flattenedCookies[] = $cookie;
                }
            }
        }

        return $flattenedCookies;
    }
}