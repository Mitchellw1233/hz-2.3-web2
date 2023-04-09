<?php

namespace Slimfony\ORM;

use Slimfony\Config\ConfigLoader;
use Slimfony\ORM\Mapping\Column;
use Slimfony\ORM\Mapping\Entity as EntityMap;
use Slimfony\ORM\Mapping\FKInterface;
use Slimfony\ORM\Mapping\Relation;
use Slimfony\ORM\Resolver\MappingResolver;

class SchemaManager
{
    public function __construct(
        protected Driver $driver,
        protected MappingResolver $mappingResolver,
        protected ConfigLoader $configLoader,
    ) {
    }

    /**
     * Assuming POSTGRESQL
     *
     * @param array<int, class-string> $entities
     * @return void
     */
    public function generate(array $entities): void
    {
        $statements = [];
        $fks = [];

        $maps = $this->mappingResolver->resolveAll($entities);

        foreach ($maps as $map) {
            $name = $map->entity->name;
//            $statements[] = 'DROP TABLE IF EXISTS ' . $name;
            $sql = "CREATE TABLE $name (";

            foreach ($map->columns as $column) {
                if ($column->autoIncrement && !str_contains(strtoupper($column->type), 'SERIAL')) {
                    throw new \InvalidArgumentException(sprintf('table `%s`.`%s`: `%s` should be a SERIAL type, 
                        because it has a autoIncrement', $map->entity->name, $column->name, $column->type));
                }

                if ($column->unique && $column->primaryKey) {
                    $column->unique = false;
                }

                $sql .= $column->name
                    . ' ' . strtoupper($column->type)
                    . ($column->primaryKey ? ' PRIMARY KEY' : '')
                    . ($column->unique ? ' UNIQUE' : '')
                    . ($column->nullable ? ' NULL' : ' NOT NULL');

                // If relation, set up FK statement, so we alter the table and add the constraint
                if ($column->hasRelation() && ($statement = $this->getRelationStatement($map->entity, $column)) !== null) {
                    $fks[] = $statement;
                }
            }

            $statements[] = $sql . ')';
        }

        foreach ($statements as $statement) {
            $this->driver->execute($statement);
        }

        foreach ($fks as $statement) {
            $this->driver->execute($statement);
        }
    }

    /**
     * Assuming POSTGRESQL
     */
    public function delete(): void
    {
        $this->driver->execute('DROP SCHEMA IF EXISTS ' . $this->configLoader->getDb()['database'] . ' CASCADE');
    }

    /**
     *
     * @param EntityMap $entity
     * @param Column $column
     *
     * @return string|null
     */
    private function getRelationStatement(EntityMap $entity, Column $column): ?string
    {
        /** @var Relation $relation Assuming it has a relation */
        $relation = $column->getRelation();

        // TODO: Relation could have other interfaces.
        if (!$relation instanceof FKInterface) {
            return null;
        }
        $relationMap = $this->mappingResolver->resolve($relation->getTargetEntity());

        return sprintf('ALTER TABLE %s ADD CONSTRAINT fk_%s_%s FOREIGN KEY (%s) REFERENCES %s (%s)',
            $entity->name,
            $entity->name,
            $column->name,
            $column->name,
            $relationMap->entity->name,
            $relation->getTargetReferenceColumn(),
        );
    }
}
