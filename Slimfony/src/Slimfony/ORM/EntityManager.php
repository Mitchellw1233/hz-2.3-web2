<?php

namespace Slimfony\ORM;

use Slimfony\ORM\Query\EntityQueryBuilder;
use Slimfony\ORM\Query\InternalQueryBuilder;

class EntityManager
{
    public function __construct(
        protected Driver $driver,
        protected MappingResolver $mappingResolver,
        protected EntityTransformer $entityTransformer,
    ) {
    }

    public function persist(Entity $entity): void
    {
        $map = $this->mappingResolver->resolve($entity::class);
        $values = $this->entityTransformer->toDBResult($entity);

        $qb = new InternalQueryBuilder();
        if ($entity->isNew()) {
            $qb->insert($map->entity->name)
                ->values($values);
        } else {
            $qb->update($map->entity->name)
                ->set($values);
        }

        if (!$this->driver->isTransaction()) {
            $this->driver->startTransaction();
        }

        $this->driver->execute($qb->build());
    }

    public function flush(): void
    {
        $this->driver->commit();
    }

    /**
     * @template T of Entity
     * @psalm-param class-string<T> $className
     * @psalm-return EntityQueryBuilder<T>
     */
    public function getQueryBuilder(string $className): EntityQueryBuilder
    {
        return new EntityQueryBuilder($this->driver, $this->entityTransformer, $this->mappingResolver, $className);
    }
}
