<?php

namespace Slimfony\Routing;

use Slimfony\Config\ConfigLoader;
use Slimfony\HttpFoundation\RequestInterface;

class RouteResolver
{
    protected ConfigLoader $config;

    public function __construct(ConfigLoader $config)
    {
        $this->config = $config;
    }

    public function resolveRoutes(): RouteCollection
    {
        $rc = new RouteCollection();

        foreach ($this->config->getRoutes() as $name => $route) {
            $rc->add(new Route($name, $route['path'], $route['controller'], $route['methods']));
        }

        return $rc;
    }

    public function resolveRoute(RequestInterface $request, RouteCollection $routeCollection): ?Route
    {
        $path = $request->getUri()->getPath();

        // split path subs
        $pathSubs = explode('/', substr($path, 1));
        $psc = count($pathSubs);

        // Remove all behind ? or # (preg_replace_callback somehow does not work correctly)
        preg_match('#[^?|\#]*#', $pathSubs[$psc-1], $pathSubsMatches);
        $pathSubs[$psc-1] = $pathSubsMatches[0];

        // check every route to see if there is a match
        foreach ($routeCollection->all() as $route) {
            $rPathSubs = explode('/', substr($route->getPath(), 1));
            $rpsc = count($rPathSubs);

            // if url has more subs than route, then we can assume to never meet requirements
            if ($psc > $rpsc) {
                continue;
            }

            $parameters = [];
            // Check if path matches
            for ($i = 1; $i < $rpsc; $i++) {
                $isDynamic = str_starts_with($rPathSubs[$i], '{');

                // if non-dynamic sub path is not set or not equal to route sub path, no match
                if (!$isDynamic && (!isset($pathSubs[$i]) || $rPathSubs[$i] !== $pathSubs[$i])) {
                    continue 2;  // next route
                }

                // if dynamic, add parameter for route key and corresponding value of the path
                if ($isDynamic) {
                    preg_match('#\{([\w\x80-\xFF]++)?(\?[^\}]*+)?\}#', $rPathSubs[$i], $m);
                    $parameters[$m[1]] = $pathSubs[$i];
                }
            }

            // if method is not supported for request
            if ($route->getMethods() !== null && !in_array($request->getMethod(), $route->getMethods(), true)) {
                continue;
            }

            $route->fillParameters($parameters);

            return $route;
        }

        return null;
    }
}
