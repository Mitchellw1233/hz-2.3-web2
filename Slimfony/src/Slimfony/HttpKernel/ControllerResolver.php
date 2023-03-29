<?php

namespace Slimfony\HttpKernel;

use Slimfony\Routing\Route;

class ControllerResolver
{
    public function getController(Route $route): callable
    {
        $controllerPath = $route->getControllerPath();

        [$class, $method] = explode('::', $controllerPath, 2);
        $controller = [new $class(), $method];

        if (!\is_callable($controller)) {
            throw new \LogicException($controllerPath.' is not callable');
        }

        return $controller;
    }

    public function getArguments(Route $route, callable $controller): array
    {
        try {
            $reflection = new \ReflectionFunction($controller(...));
            $cParams = $reflection->getParameters();
            $rParams = $route->getParameters();

            foreach ($cParams as $param) {
                if (!\key_exists($param->getName(), $rParams) && !$param->isOptional()) {
                    throw new \LogicException("Didn't include ".$param->getName()." inside of ".$reflection->getName());
                }
            }
        } catch (\Exception $e) {
            throw new \LogicException('The controller apperantly doesn`t exist?');
        }

        return \array_values($route->getParameters());
    }
}