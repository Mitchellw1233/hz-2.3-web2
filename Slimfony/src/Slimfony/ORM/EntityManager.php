<?php

namespace Slimfony\ORM;

use Slimfony\ORM\Query\EntityQueryBuilder;
use Slimfony\ORM\Query\InternalQueryBuilder;
use Slimfony\ORM\Resolver\MappingResolver;

class EntityManager
{
    public function __construct(
        protected Driver $driver,
        protected MappingResolver $mappingResolver,
        protected EntityTransformer $entityTransformer,
    ) {
    }

    /**
     * @template T
     * @psalm-param Entity<T> $entity
     * @psalm-return T
     */
    public function persist(Entity $entity, string $pk = 'id', string $pkProp = 'id')
    {
        $map = $this->mappingResolver->resolve($entity::class);
        $values = $this->entityTransformer->toDBResult($entity);

        $qb = new InternalQueryBuilder();
        if ($entity->isNew()) {
            $qb->insert($map->entity->name)
                ->values($values)
                ->returning();
        } else {
            // Get pk value
            try {
                $rp = new \ReflectionProperty($entity::class, $pkProp);
            } catch (\ReflectionException $e) {
                throw new \InvalidArgumentException($e->getMessage());
            }
            $rp->setAccessible(true);
            $pkValue = $rp->getValue($entity);

            $qb->update($map->entity->name)
                ->set($values)
                ->where(sprintf('%s = :%s', $pk, $pk))
                ->setParameters([
                    $pk => $pkValue,
                ])->returning();
        }

        if (!$this->driver->inTransaction()) {
            $this->driver->beginTransaction();
        }

        return $this->entityTransformer->fromDBResult(
            $entity::class,
            $this->driver->execute($qb->build(), $qb->getParameters())[0]
        );
    }

    public function remove(Entity $entity, string $pk = 'id', string $pkProp = 'id'): void
    {
        $map = $this->mappingResolver->resolve($entity::class);

        // Get pk value
        try {
            $rp = new \ReflectionProperty($entity::class, $pkProp);
        } catch (\ReflectionException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }
        $rp->setAccessible(true);
        $pkValue = $rp->getValue($entity);

        $qb = new InternalQueryBuilder();
        $qb
            ->delete($map->entity->name)
            ->where(sprintf('%s = :%s', $pk, $pk))
            ->setParameters([
                $pk => $pkValue,
            ]);

        if (!$this->driver->inTransaction()) {
            $this->driver->beginTransaction();
        }

        $this->driver->execute($qb->build(), $qb->getParameters());
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
