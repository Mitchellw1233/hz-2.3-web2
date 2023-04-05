<?php

namespace Slimfony\ORM\Resolver;

use Slimfony\ORM\Mapping\Entity;
use Slimfony\ORM\Mapping\Column;

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
     * @return array{
     *     entity: Entity,
     *     columns: array<string, Column>
     * }
     */
    public function resolve(string $entity): array
    {
        try {
            $rEntity = new \ReflectionClass($entity);
        } catch (\ReflectionException) {
            throw new \LogicException('Entity "'.$entity.'" does not exist');
        }

        $map = [];
        $map['entity'] = $rEntity->getAttributes()[0]->newInstance();

        foreach ($rEntity->getProperties() as $property) {
            $propertyAttribute = $property->getAttributes()[0];
            if ($propertyAttribute === null) {
                continue;
            }

            $map['columns'][$property->getName()] = $propertyAttribute->newInstance();
        }

        return $map;
    }

    /**
     * @return array<int, array{
     *          entity: Entity,
     *          columns: array<string, Column>
     *      }>
     */
    public function resolveAll(): array {
        $mapping = [];

        foreach ($this->entities as $entity) {
            $mapping[] = $this->resolve($entity);
        }

        return $mapping;
    }
}