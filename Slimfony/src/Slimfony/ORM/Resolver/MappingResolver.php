<?php

namespace Slimfony\ORM\Resolver;

use Slimfony\ORM\Mapping\Entity;
use Slimfony\ORM\Mapping\Column;
use Slimfony\ORM\Mapping\Map;

class MappingResolver
{
    /**
     * @var array<int, class-string>
     */
    protected array $entities;

    /**
     * @param array<int, class-string> $entities as FQN
     */
    public function __construct(array $entities)
    {
        $this->entities = $entities;
    }

    /**
     * @param string $entity
     * @return Map
     */
    public function resolve(string $entity): Map
    {
        try {
            $rEntity = new \ReflectionClass($entity);
        } catch (\ReflectionException) {
            throw new \LogicException('Entity "'.$entity.'" does not exist');
        }

        $map = new Map($rEntity->getAttributes()[0]->newInstance(), []);

        foreach ($rEntity->getProperties() as $property) {
            $propertyAttribute = $property->getAttributes()[0];
            if ($propertyAttribute === null) {
                continue;
            }

            $map->columns[$property->getName()] = $propertyAttribute->newInstance();
        }

        return $map;
    }

    /**
     * @return array<int, Map>
     */
    public function resolveAll(): array {
        $mapping = [];

        foreach ($this->entities as $entity) {
            $mapping[] = $this->resolve($entity);
        }

        return $mapping;
    }
}