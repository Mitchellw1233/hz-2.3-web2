<?php

namespace Slimfony\ORM;

use Slimfony\ORM\Query\EntityQueryBuilder;
use Slimfony\ORM\Query\InternalQueryBuilder;
use Slimfony\ORM\Resolver\EntityValueResolver;

class EntityManager
{
    public function __construct(
        protected Driver $driver,
        protected MappingResolver $mappingResolver,
        protected EntityValueResolver $entityValueResolver,
        protected EntityFactory $entityFactory,
    ) {
    }

    public function persist(Entity $entity): void
    {
        $map = $this->mappingResolver->resolve($entity::class);
        $values = [];

        foreach ($this->entityValueResolver->resolve($entity, $map) as $entityValue) {
            $values[$entityValue['mapColumn']->name] = $entityValue['value'];
        }

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
        return new EntityQueryBuilder($this->driver, $this->entityFactory, $this->mappingResolver, $className);
    }
}
