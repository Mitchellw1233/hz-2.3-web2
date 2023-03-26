<?php

namespace Slimfony\HttpFoundation\Bag;


/**
 * @extends AbstractBag<string, mixed>
 */
class ServerBag extends AbstractBag
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
    }

    /**
     * Gets the HTTP headers.
     *
     * @return array<string, mixed>
     */
    public function getHeaders(): array
    {
        $headers = [];
        foreach ($this->data as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headers[substr($key, 5)] = $value;
            } elseif (\in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5'], true)) {
                $headers[$key] = $value;
            }
        }

        if (isset($this->data['PHP_AUTH_USER'])) {
            $headers['PHP_AUTH_USER'] = $this->data['PHP_AUTH_USER'];
            $headers['PHP_AUTH_PW'] = $this->data['PHP_AUTH_PW'] ?? '';
        } else {
            /*
             * php-cgi under Apache does not pass HTTP Basic user/pass to PHP by default
             * For this workaround to work, add these lines to your .htaccess file:
             * RewriteCond %{HTTP:Authorization} .+
             * RewriteRule ^ - [E=HTTP_AUTHORIZATION:%0]
             *
             * A sample .htaccess file:
             * RewriteEngine On
             * RewriteCond %{HTTP:Authorization} .+
             * RewriteRule ^ - [E=HTTP_AUTHORIZATION:%0]
             * RewriteCond %{REQUEST_FILENAME} !-f
             * RewriteRule ^(.*)$ app.php [QSA,L]
             */

            $authorizationHeader = null;
            if (isset($this->data['HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $this->data['HTTP_AUTHORIZATION'];
            } elseif (isset($this->data['REDIRECT_HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $this->data['REDIRECT_HTTP_AUTHORIZATION'];
            }

            if (null !== $authorizationHeader) {
                if (0 === stripos($authorizationHeader, 'basic ')) {
                    // Decode AUTHORIZATION header into PHP_AUTH_USER and PHP_AUTH_PW when authorization header is basic
                    $exploded = explode(':', base64_decode(substr($authorizationHeader, 6)), 2);
                    if (2 == \count($exploded)) {
                        [$headers['PHP_AUTH_USER'], $headers['PHP_AUTH_PW']] = $exploded;
                    }
                } elseif (empty($this->data['PHP_AUTH_DIGEST']) && (0 === stripos($authorizationHeader, 'digest '))) {
                    // In some circumstances PHP_AUTH_DIGEST needs to be set
                    $headers['PHP_AUTH_DIGEST'] = $authorizationHeader;
                    $this->data['PHP_AUTH_DIGEST'] = $authorizationHeader;
                } elseif (0 === stripos($authorizationHeader, 'bearer ')) {
                    /*
                     * XXX: Since there is no PHP_AUTH_BEARER in PHP predefined variables,
                     *      I'll just set $headers['AUTHORIZATION'] here.
                     *      https://php.net/reserved.variables.server
                     */
                    $headers['AUTHORIZATION'] = $authorizationHeader;
                }
            }
        }

        if (isset($headers['AUTHORIZATION'])) {
            return $headers;
        }

        // PHP_AUTH_USER/PHP_AUTH_PW
        if (isset($headers['PHP_AUTH_USER'])) {
            $headers['AUTHORIZATION'] = 'Basic '.base64_encode($headers['PHP_AUTH_USER'].':'.($headers['PHP_AUTH_PW'] ?? ''));
        } elseif (isset($headers['PHP_AUTH_DIGEST'])) {
            $headers['AUTHORIZATION'] = $headers['PHP_AUTH_DIGEST'];
        }

        return $headers;
    }

    // TODO: until "@template" fixed
    /**
     * @inheritDoc
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return parent::get($key, $default);
    }

    /**
     * @inheritDoc
     *
     * @param string|null $key
     *
     * @return array<string, mixed>
     */
    public function all($key = null): array
    {
        return parent::all($key);
    }

    /**
     * @inheritDoc
     *
     * @return string[]
     */
    public function keys(): array
    {
        return parent::keys();
    }

    /**
     * @inheritDoc
     *
     * @param string $key
     */
    public function has($key): bool
    {
        return parent::has($key);
    }

    /**
     * @inheritDoc
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value): void
    {
        parent::set($key, $value);
    }

    /**
     * @inheritDoc
     *
     * @param array<string, mixed> $data
     */
    public function add(array $data = []): void
    {
        parent::add($data);
    }

    /**
     * @inheritDoc
     *
     * @param string $key
     */
    public function remove($key): void
    {
        parent::remove($key);
    }

    /**
     * @inheritDoc
     *
     * @param array<string, mixed> $data
     */
    public function replace(array $data = []): void
    {
        parent::replace($data);
    }

    /**
     * @inheritDoc
     *
     * @param string $key
     * @param mixed $value
     */
    public function contains($key, $value): bool
    {
        return parent::contains($key, $value);
    }
}
