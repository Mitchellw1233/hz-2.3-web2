<?php

namespace Slimfony\Routing;

use Slimfony\HttpFoundation\Request;

class RouteCollection
{
    /**
     * @var array<Route>
     */
    protected array $routes;

    /**
     * @param array<Route> $routes
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
        $this->routes[] = $route;
    }

    /**
     * @param Route $route
     * @return void
     */
    public function remove(Route $route): void
    {
        if (false !== $key = array_search($route, $this->routes)) {
            unset($this->routes[$key]);
        }
    }

    /**
     * Returns all routes
     * @return Array<Route>
     */
    public function all(): array
    {
        return $this->routes;
    }
}
