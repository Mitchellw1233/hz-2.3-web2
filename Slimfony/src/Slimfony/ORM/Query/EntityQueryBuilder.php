<?php

namespace Slimfony\ORM\Query;

use Slimfony\ORM\EntityTransformer;

/**
 * @template T
 */
class EntityQueryBuilder extends AbstractQueryBuilder
{
    protected bool $isSingleResult = false;

    /**
     * @param Driver $driver
     * @param EntityTransformer $entityTransformer
     * @param MappingResolver $mappingResolver
     * @psalm-param class-string<T> $className
     */
    public function __construct(
        protected Driver $driver,
        protected EntityTransformer $entityTransformer,
        protected MappingResolver $mappingResolver,
        protected string $className
    ) {
        parent::__construct();
        $this->statements[] = 'SELECT * FROM ' . $this->mappingResolver->reslove($this->className)->entity->name;
    }

    /**
     * * T|null when limit(1)
     * * T[] when no limit(1)
     *
     * @psalm-return T[]|T|null
     */
    public function result()
    {
        $result = $this->driver->execute($this->build(), $this->getParameters());

        if ($this->isSingleResult) {
            return $this->entityTransformer->fromDBResult($this->className, $result);
        }

        $entities = [];
        foreach ($result as $entityResult) {
            $entities[] = $this->entityTransformer->fromDBResult($this->className, $entityResult);
        }

        return $entities;
    }

    /**
     * @param string $condition example: user.name = :name or p.name = :name
     *
     * @return static
     */
    public function where(string $condition): static
    {
        $this->statements[] = 'WHERE ' . $condition;

        return $this;
    }

    public function groupBy(string ...$columns): static
    {
        $this->statements[] = 'GROUP BY ' . implode(', ', $columns);
        return $this;
    }

    public function having(string $condition): static
    {
        $this->statements[] = 'HAVING ' . $condition;
        return $this;
    }

    public function limit(int $limit): static
    {
        if ($limit < 2) {
            $this->isSingleResult = true;
        }

        $this->statements[] = 'LIMIT ' . $limit;
        return $this;
    }

    public function orderBy(OrderByEnum $order, string ...$columns): static
    {
        $this->statements[] = 'ORDER BY ' . implode(', ', $columns) . $order->name;
        return $this;
    }
}
