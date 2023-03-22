<?php
declare(strict_types=1);

namespace Slimfony\HttpFoundation;

use Slimfony\HttpFoundation\Bag\HeaderBag;
use Slimfony\HttpFoundation\Bag\InputBag;
use Slimfony\HttpFoundation\Bag\ParameterBag;
use Slimfony\HttpFoundation\Bag\ServerBag;
use Slimfony\HttpFoundation\Session\Session;
use Slimfony\HttpFoundation\Session\SessionInterface;

class Request implements RequestInterface
{
    public const METHOD_HEAD = 'HEAD';
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_PATCH = 'PATCH';
    public const METHOD_DELETE = 'DELETE';
    public const METHOD_PURGE = 'PURGE';
    public const METHOD_OPTIONS = 'OPTIONS';
    public const METHOD_TRACE = 'TRACE';
    public const METHOD_CONNECT = 'CONNECT';

    /**
     * Custom parameters.
     *
     * @var ParameterBag
     */
    public ParameterBag $attributes;

    /**
     * Request body parameters ($_POST).
     *
     * @var InputBag
     */
    public InputBag $request;

    /**
     * Query string parameters ($_GET).
     *
     * @var InputBag
     */
    public InputBag $query;

    /**
     * Server and execution environment parameters ($_SERVER).
     *
     * @var ServerBag
     */
    public ServerBag $server;

//    /**
//     * Uploaded files ($_FILES).
//     *
//     * @var FileBag
//     */
//    public FileBag $files = null;

    /**
     * Cookies ($_COOKIE).
     *
     * @var InputBag
     */
    public InputBag $cookies;

    /**
     * Headers (taken from the $_SERVER).
     *
     * @var HeaderBag
     */
    public HeaderBag $headers;

    /**
     * @var SessionInterface|null
     */
    protected ?SessionInterface $session;

    protected UriInterface $uri;

    protected string $method;

    protected bool $isSecure;

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [])
    {
        $this->request = new InputBag($request);
        $this->query = new InputBag($query);
        $this->attributes = new ParameterBag($attributes);
        $this->cookies = new InputBag($cookies);
//        $this->files = new FileBag($files);
        $this->server = new ServerBag($server);
        $this->headers = new HeaderBag($this->server->getHeaders());

        $this->uri = new Uri(sprintf(
            '%s://%s%s',
            $this->isSecure() ? 'https' : 'http',
            $this->server->get('HTTP_HOST'),
            $this->server->get('REQUEST_URI')
        ));
        $this->method = strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
    }

    /**
     * Creates a new request with values from PHP's super globals.
     */
    public static function createFromGlobals(): static
    {
        return new static($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER);
    }

    public function getSession(): SessionInterface
    {
        if ($this->session === null) {
            $this->session = new Session();
        }

        return $this->session;
    }

    public function getHeaders(): HeaderBag
    {
        return $this->headers;
    }

    public function getServer(): ServerBag
    {
        return $this->server;
    }

    public function getCookies(): InputBag
    {
        return $this->cookies;
    }

    public function getQuery(): InputBag
    {
        return $this->query;
    }

    public function getAttributes(): ParameterBag
    {
        return $this->attributes;
    }

    public function getRequestTarget(): string
    {
        $target = $this->getUri()->getPath();
        if ($this->uri->getQueryString()) {
            $target .= '?' . $this->uri->getQueryString();
        }
        return $target;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
    {
        $this->method = strtoupper($method);
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function getProtocolVersion(): string
    {
        return $this->server->get('SERVER_PROTOCOL');
    }

    public function isSecure(): bool
    {
        if (!isset($this->isSecure)) {
            $https = $this->server->get('HTTPS');
            $this->isSecure = !empty($https) && strtolower($https) !== 'off';
        }

        return $this->isSecure;
    }
}
