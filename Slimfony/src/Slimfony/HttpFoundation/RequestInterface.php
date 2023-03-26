<?php

namespace Slimfony\HttpFoundation;

use Slimfony\HttpFoundation\Bag\InputBag;
use Slimfony\HttpFoundation\Bag\ParameterBag;
use Slimfony\HttpFoundation\Bag\ServerBag;
use Slimfony\HttpFoundation\Session\SessionInterface;

interface RequestInterface extends MessageInterface
{
    /**
     * Retrieve server parameters.
     *
     * Retrieves data related to the incoming request environment,
     * typically derived from PHP's $_SERVER superglobal. The data IS NOT
     * REQUIRED to originate from $_SERVER.
     *
     * TODO: Exclude cookies as we use $_COOKIE for cookie management?
     *
     * @return ServerBag
     */
    public function getServer(): ServerBag;

    /**
     * Retrieve cookies.
     *
     * Retrieves cookies sent by the client to the server.
     *
     * The data MUST be compatible with the structure of the $_COOKIE
     * superglobal.
     *
     * @return InputBag
     */
    public function getCookies(): InputBag;

    /**
     * Retrieve query string arguments in InputBag.
     *
     * Retrieves the deserialized query string arguments, if any.
     *
     * Note: the query params might not be in sync with the URI or server
     * params. If you need to ensure you are only getting the original
     * values, you may need to parse the query string from `getUri()->getQuery()`
     * or from the `QUERY_STRING` server param.
     *
     * @return InputBag
     */
    public function getQuery(): InputBag;

    /**
     * Retrieve attributes derived from the request.
     *
     * The request "attributes" may be used to allow injection of any
     * parameters derived from the request: e.g., the results of path
     * match operations; the results of decrypting cookies; the results of
     * deserializing non-form-encoded message bodies; etc. Attributes
     * will be application and request specific, and CAN be mutable.
     *
     * @return ParameterBag Attributes derived from the request.
     */
    public function getAttributes(): ParameterBag;

    /**
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
    public function getRequestTarget(): string;

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod(): string;

    /**
     * Gets the HTTP method of the request.
     *
     * @param string $method
     */
    public function setMethod(string $method): void;

    /**
     * Retrieves the URI instance.
     *
     * This method MUST return a UriInterface instance.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface Returns a UriInterface instance
     *     representing the URI of the request.
     */
    public function getUri(): UriInterface;

    /**
     * Retrieves the current session or creates one if it does not exist
     *
     * @return SessionInterface Returns the current session.
     */
    public function getSession(): SessionInterface;
}
