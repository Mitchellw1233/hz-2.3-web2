<?php

namespace Slimfony\ORM\Query;

class InternalQueryBuilder extends AbstractQueryBuilder
{
    public function from(string $table, string $alias = null): static
    {
        $this->statements[] = 'FROM ' . $table . ($alias ? ' ' . $alias : '');

        return $this;
    }

    // Select
    public function select(string ...$columns): static
    {
        $this->statements[] = 'SELECT ' . implode(', ', $columns);

        return $this;
    }

    public function selectDistinct(string ...$columns): static
    {
        $this->statements[] = 'SELECT DISTINCT ' . implode(', ', $columns);

        return $this;
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
        $this->statements[] = 'LIMIT ' . $limit;
        return $this;
    }

    public function orderBy(OrderByEnum $order, string ...$columns): static
    {
        $this->statements[] = 'ORDER BY ' . implode(', ', $columns) . $order->name;
        return $this;
    }

    // Insert
    public function insert(string $table): static
    {
        $this->statements[] = 'INSERT INTO ' . $table;
        return $this;
    }

    /**
     * Automatically sets parameters
     *
     * @param array<string, string> $inserts ['column' => ':param']
     *
     * @return $this
     */
    public function values(array $inserts): static
    {
        $paramKeys = [];
        foreach ($inserts as $key => $value) {
            $paramKeys[] = ':' . $key;
        }

        $this->statements[] = sprintf('(%s) VALUES (%s)',
            implode(', ', array_keys($inserts)),
            implode(', ', $paramKeys)
        );
        $this->setParameters($inserts);

        return $this;
    }

    // Update
    public function update(string $table): static
    {
        $this->statements[] = 'UPDATE ' . $table;
        return $this;
    }

    /**
     * Automatically sets parameters
     *
     * @param array<string, string> $updates ['column' => ':param']
     *
     * @return $this
     */
    public function set(array $updates): static
    {
        $values = [];
        foreach ($updates as $column => $value) {
            $values[] = sprintf('%s = :%s', $column, $column);
        }
        $this->statements[] = 'SET ' . implode(', ', $values);
        $this->setParameters($updates);

        return $this;
    }

    // Delete
    public function delete(string $table): static
    {
        $this->statements[] = 'DELETE FROM ' . $table;
        return $this;
    }

    public function returning(string $clause = '*'): static
    {
        $this->statements[] = 'RETURNING ' . $clause;
        return $this;
    }
}
