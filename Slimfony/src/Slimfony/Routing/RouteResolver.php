<?php

namespace Slimfony\Routing;

use Slimfony\Config\ConfigLoader;

class RouteResolver
{
    protected ConfigLoader $config;

    public function __construct(ConfigLoader $config)
    {
        $this->config = $config;
    }

    public function resolve(): RouteCollection
    {
        $rc = new RouteCollection();

        foreach ($this->config->getRoutes() as $name => $route) {
            $rc->add(new Route($name, $route['path'], $route['controller'], $route['methods']));
        }

        return $rc;
    }
}
