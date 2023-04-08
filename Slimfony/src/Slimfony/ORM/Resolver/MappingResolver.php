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
            $propertyAttributes = $property->getAttributes();

            // Is true if property has 2 attributes in our case (FKRelation and Column)
            // Elseif checks if index 0 is not null so we can make a new instance on it
            // Else we just continue
            if (count($propertyAttributes) == 2) {
                $fkRelation = $propertyAttributes[0]->newInstance();
                $column = $propertyAttributes[1]->newInstance();
                $column->setRelation($fkRelation);
            } elseif($propertyAttributes[0] !== null) {
                $column = $propertyAttributes[0]->newInstance();
            } else {
                continue;
            }

            $map->columns[$property->getName()] = $column;
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