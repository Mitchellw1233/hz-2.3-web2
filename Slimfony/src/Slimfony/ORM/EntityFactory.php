<?php

namespace Slimfony\ORM;

class EntityFactory
{
    /**
     * @param array<string, mixed> $arr
     *
     * @template T
     * @psalm-param class-string<T> $class
     * @psalm-return T
     */
    public function createFromArray(string $className, array $arr)
    {
        try {
            $rc = new \ReflectionClass($className);
        } catch (\ReflectionException $e) {
            throw new \LogicException($e->getMessage());
        }

        // Map parameter names to values from associative array
        $args = [];
        foreach ($rc->getConstructor()?->getParameters() as $param) {
            $name = $param->getName();
            if (!array_key_exists($name, $arr) && !$param->isOptional()) {
                throw new \InvalidArgumentException("Missing required parameter $name");
            }

            $args[] = $arr[$name];
        }

        try {
            return new $className(...$args);
        } catch (\Exception $e) {
            throw new \LogicException($e->getMessage());
        }
    }
}
