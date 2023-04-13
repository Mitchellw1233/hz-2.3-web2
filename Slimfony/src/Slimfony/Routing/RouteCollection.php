<?php

namespace Slimfony\Routing;


class RouteCollection
{
    /**
     * @var array<string, Route>
     */
    protected array $routes;

    /**
     * @param array<string, Route> $routes
     */
    public function __construct(array $routes=[])
    {
        $this->routes = $routes;
    }

    /**
     * @param Route $route
     * @return void
     */
    public function add(Route $route): void
    {
        $this->routes[$route->getName()] = $route;
    }

    /**
     * @param Route $route
     * @return void
     */
    public function remove(Route $route): void
    {
        if (array_key_exists($route->getName(), $this->routes)) {
            unset($this->routes[$route->getName()]);
        }
    }

    /**
     * Returns all routes
     *
     * @return array<string, Route>
     */
    public function all(): array
    {
        return $this->routes;
    }
}
