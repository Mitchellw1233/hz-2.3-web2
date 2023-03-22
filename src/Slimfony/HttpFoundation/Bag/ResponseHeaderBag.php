<?php

namespace Slimfony\HttpFoundation\Bag;

use Slimfony\HttpFoundation\Cookie;

class ResponseHeaderBag extends HeaderBag
{
    protected array $cookies = [];
    protected array $headerNames = [];
    public function __construct(array $headers = [])
    {
        parent::__construct($headers);

        if (!isset($this->data['headers'])) {
            $this->set('Date', gmdate(\DATE_RFC3339).' GMT');
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

    public function getCookies(): array
    {
        return $this->cookies;
    }
}