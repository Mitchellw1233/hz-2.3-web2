<?php

namespace Slimfony\ORM\Factory;

class EntityFactory
{
    /**
     * @param array $arr
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
        foreach ($rc->getConstructor()->getParameters() as $param) {
            $name = $param->getName();
            if (!$param->isOptional() && !array_key_exists($name, $arr)) {
                throw new \InvalidArgumentException("Missing required parameter $name");
            }

            $args[] = $arr[$name];
        }

        return new $className(...$args);
    }
}
