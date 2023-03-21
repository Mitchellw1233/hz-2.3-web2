<?php
declare(strict_types=1);

namespace Slimfony\HttpFoundation;

use Slimfony\HttpFoundation\Bag\HeaderBag;
use Slimfony\HttpFoundation\Bag\InputBag;
use Slimfony\HttpFoundation\Bag\ParameterBag;
use Slimfony\HttpFoundation\Bag\ServerBag;
use Slimfony\HttpFoundation\Exception\ConflictingHeadersException;
use Slimfony\HttpFoundation\Exception\JsonException;
use Slimfony\HttpFoundation\Exception\SessionNotFoundException;
use Slimfony\HttpFoundation\Exception\SuspiciousOperationException;
use Slimfony\HttpFoundation\Session\Session;
use Slimfony\HttpFoundation\Session\SessionInterface;
use Slimfony\HttpFoundation\Utils\AcceptHeader;
use Slimfony\HttpFoundation\Utils\HeaderUtils;
use Slimfony\HttpFoundation\Utils\IpUtils;

class Request
{
    public const HEADER_FORWARDED = 0b000001; // When using RFC 7239
    public const HEADER_X_FORWARDED_FOR = 0b000010;
    public const HEADER_X_FORWARDED_HOST = 0b000100;
    public const HEADER_X_FORWARDED_PROTO = 0b001000;
    public const HEADER_X_FORWARDED_PORT = 0b010000;
    public const HEADER_X_FORWARDED_PREFIX = 0b100000;

    public const HEADER_X_FORWARDED_AWS_ELB = 0b0011010; // AWS ELB doesn't send X-Forwarded-Host
    public const HEADER_X_FORWARDED_TRAEFIK = 0b0111110; // All "X-Forwarded-*" headers sent by Traefik reverse proxy

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
     * @var string[]
     */
    protected static array $trustedProxies = [];

    /**
     * @var string[]
     */
    protected static array $trustedHostPatterns = [];

    /**
     * @var string[]
     */
    protected static array $trustedHosts = [];

    protected static bool $httpMethodParameterOverride = false;

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

//    /**
//     * @var string|resource|false|null
//     */
//    protected $content;
//
//    /**
//     * @var string[]|null
//     */
//    protected ?array $languages;
//
//    /**
//     * @var string[]|null
//     */
//    protected ?array $charsets;
//
//    /**
//     * @var string[]|null
//     */
//    protected ?array $encodings;
//
//    /**
//     * @var string[]|null
//     */
//    protected ?array $acceptableContentTypes;
//
//    /**
//     * @var string|null
//     */
//    protected ?string $pathInfo;
//
//    /**
//     * @var string|null
//     */
//    protected ?string $requestUri;
//
//    /**
//     * @var string|null
//     */
//    protected ?string $baseUrl;
//
//    /**
//     * @var string|null
//     */
//    protected ?string $basePath;
//
//    /**
//     * @var string|null
//     */
//    protected ?string $method;
//
//    /**
//     * @var string|null
//     */
//    protected ?string $format;
//
//    /**
//     * @var string|null
//     */
//    protected ?string $locale;
//
//    /**
//     * @var string
//     */
//    protected string $defaultLocale = 'en';
//
//    /**
//     * @var array<string, string[]>|null
//     */
//    protected static ?array $formats;
//
//    protected static $requestFactory;
//
//    private ?string $preferredFormat = null;
//    private bool $isHostValid = true;
//    private bool $isForwardedValid = true;
//    private bool $isSafeContentPreferred;
//
//    private static int $trustedHeaderSet = -1;

    private const FORWARDED_PARAMS = [
        self::HEADER_X_FORWARDED_FOR => 'for',
        self::HEADER_X_FORWARDED_HOST => 'host',
        self::HEADER_X_FORWARDED_PROTO => 'proto',
        self::HEADER_X_FORWARDED_PORT => 'host',
    ];

    /**
     * Names for headers that can be trusted when
     * using trusted proxies.
     *
     * The FORWARDED header is the standard as of rfc7239.
     *
     * The other headers are non-standard, but widely used
     * by popular reverse proxies (like Apache mod_proxy or Amazon EC2).
     */
    private const TRUSTED_HEADERS = [
        self::HEADER_FORWARDED => 'FORWARDED',
        self::HEADER_X_FORWARDED_FOR => 'X_FORWARDED_FOR',
        self::HEADER_X_FORWARDED_HOST => 'X_FORWARDED_HOST',
        self::HEADER_X_FORWARDED_PROTO => 'X_FORWARDED_PROTO',
        self::HEADER_X_FORWARDED_PORT => 'X_FORWARDED_PORT',
        self::HEADER_X_FORWARDED_PREFIX => 'X_FORWARDED_PREFIX',
    ];

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        $this->request = new InputBag($request);
        $this->query = new InputBag($query);
        $this->attributes = new ParameterBag($attributes);
        $this->cookies = new InputBag($cookies);
//        $this->files = new FileBag($files);
        $this->server = new ServerBag($server);
        $this->headers = new HeaderBag($this->server->getHeaders());

//        $this->content = $content;
//        $this->languages = null;
//        $this->charsets = null;
//        $this->encodings = null;
//        $this->acceptableContentTypes = null;
//        $this->pathInfo = null;
//        $this->requestUri = null;
//        $this->baseUrl = null;
//        $this->basePath = null;
//        $this->method = null;
//        $this->format = null;
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
}
