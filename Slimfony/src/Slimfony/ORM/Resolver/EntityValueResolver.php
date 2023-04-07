<?php

namespace Slimfony\ORM\Resolver;

use Slimfony\ORM\Entity;

// TODO: Remove
class EntityValueResolver
{
    /**
     * @param Entity $entity
     * @param Map $map
     * @return array<int, array{value: mixed, mapColumn: Column}> property => value
     */
    public function resolve(Entity $entity, Map $map): array
    {
        $values = [];

        foreach ($map->columns as $propName => $column) {
            try {
                $rp = new \ReflectionProperty($entity, $propName);
            } catch (\ReflectionException $e) {
                throw new \LogicException($e->getMessage());
            }
            $rp->setAccessible(true);

            $values[] = ['value' => $rp->getValue(), 'mapColumn' => $column];
        }

        return $values;
    }
}
