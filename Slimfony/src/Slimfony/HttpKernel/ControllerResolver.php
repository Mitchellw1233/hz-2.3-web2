<?php

namespace Slimfony\HttpKernel;

use Slimfony\DependencyInjection\Container;
use Slimfony\Routing\Route;

class ControllerResolver
{
    public function __construct(
        protected Container $container
    ) {
    }

    /**
     * @param Route $route
     *
     * @return callable
     */
    public function getController(Route $route): callable
    {
        $controllerPath = $route->getControllerPath();

        [$class, $method] = explode('::', $controllerPath, 2);
        try {
            $class = $this->container->get($class);
        } catch (\Exception) {
            throw new \LogicException($controllerPath.' is not set in services.php');
        }
        $controller = [$class, $method];

        if (!\is_callable($controller)) {
            throw new \LogicException($controllerPath.' is not callable');
        }

        return $controller;
    }

    /**
     * @param Route $route
     * @param callable $controller
     *
     * @return array<int, string>
     */
    public function getArguments(Route $route, callable $controller): array
    {
        try {
            $reflection = new \ReflectionFunction($controller(...));
            $cParams = $reflection->getParameters();
            $rParams = $route->getParameters();

            foreach ($cParams as $param) {
                if (!\array_key_exists($param->getName(), $rParams) && !$param->isOptional()) {
                    throw new \LogicException("Didn't include ".$param->getName()." inside of ".$reflection->getName());
                }
            }
        } catch (\ReflectionException) {
            throw new \LogicException('The controller apparently doesn`t exist?');
        }

        return \array_values($route->getParameters());
    }
}
