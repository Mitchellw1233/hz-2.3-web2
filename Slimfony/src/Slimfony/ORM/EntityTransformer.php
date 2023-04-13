<?php

namespace Slimfony\ORM;

use Slimfony\ORM\Exception\NoRelationResultException;
use Slimfony\ORM\Mapping\Column;
use Slimfony\ORM\Mapping\FKInterface;
use Slimfony\ORM\Mapping\Relation;
use Slimfony\ORM\Query\EntityQueryBuilder;
use Slimfony\ORM\Resolver\MappingResolver;

class EntityTransformer
{
    public function __construct(
        protected MappingResolver $mappingResolver,
        protected Driver $driver,
    )
    {
    }

    /**
     * @param string $className
     * @param array<string, mixed> $dbResult
     * @param bool $isNew
     *
     * @template T of Entity
     * @psalm-param class-string<T> $class
     * @psalm-return T
     */
    public function fromDBResult(string $className, array $dbResult, bool $isNew = false)
    {
        try {
            $rc = new \ReflectionClass($className);
            $isNewProp = new \ReflectionProperty(Entity::class, 'isNew');
            $class = $rc->newInstanceWithoutConstructor();
        } catch (\ReflectionException $e) {
            throw new \LogicException($e->getMessage());
        }
        // Set isNew
        $isNewProp->setAccessible(true);
        $isNewProp->setValue($class, $isNew);

        // Loop through columns and set the property given by column: to value of $dbResult
        $map = $this->mappingResolver->resolve($className);
        foreach ($map->columns as $propName => $column) {
            try {
                $prop = new \ReflectionProperty($className, $propName);
            } catch (\ReflectionException $e) {
                throw new \LogicException($e->getMessage());
            }
            $prop->setAccessible(true);

            // If column has relation, need to set all the values
            if ($column->hasRelation()) {
                $prop->setValue($class, $this->fromRelationResult($column, $dbResult));
                continue;
            }

            // If column name not in dbResult
            if (!array_key_exists($column->name, $dbResult)) {
                throw new \LogicException(sprintf('column `%s` is not in result set', $column->name));
            }

            preg_match('#^[^\(]*#', $column->type, $shortType);
            $shortType = $shortType[0];

            $value = $dbResult[$column->name];
            if (array_key_exists($shortType, DBTypeMapper::types())) {
                $value = DBTypeMapper::types()[$shortType]['from']($value);
            }

            $prop->setValue($class, $value);
        }

        return $class;
    }

    /**
     * @param Entity $entity
     *
     * @return array<string, mixed>
     */
    public function toDBResult(Entity $entity): array
    {
        $result = [];
        $map = $this->mappingResolver->resolve($entity::class);
        foreach ($map->columns as $propName => $column) {
            if ($column->autoIncrement) {
                continue;
            }

            try {
                $prop = new \ReflectionProperty($entity::class, $propName);
            } catch (\ReflectionException $e) {
                throw new \LogicException($e->getMessage());
            }
            $prop->setAccessible(true);
            $value = $prop->getValue($entity);

            if ($value instanceof Entity && $column->hasRelation()) {
                try {
                    $value = $this->toRelationResult($entity, $column, $value);
                } catch (NoRelationResultException) {
                    continue;
                }
            }

            preg_match('#^[^\(]*#', $column->type, $shortType);
            $shortType = $shortType[0];

            if (array_key_exists($shortType, DBTypeMapper::types())) {
                $value = DBTypeMapper::types()[$shortType]['to']($value);
            }

            $result[$column->name] = $value;
        }

        return $result;
    }

    /**
     * @param Column $column
     * @param array<string, mixed> $dbResult
     * @return Entity
     */
    protected function fromRelationResult(Column $column, array $dbResult): Entity
    {
        if (!array_key_exists($column->name, $dbResult)) {
            throw new \LogicException(sprintf('column `%s` is not in result set', $column->name));
        }

        /** @var Relation $relation already checked if it has a relation, see fromDBResult() */
        $relation = $column->getRelation();
        $qb = new EntityQueryBuilder($this->driver, $this, $this->mappingResolver, $relation->getTargetEntity());

        // If it has a FK, return the referenced entity
        if ($relation instanceof FKInterface) {
            return $qb
                ->where($relation->getTargetReferenceColumn() . ' = :value')
                ->setParameters([
                    'value' => $dbResult[$column->name],
                ])
                ->limit(1)
                ->result();
        }

        // TODO: check if single result expected, if so, return with mappedBy and referencedCol etc.
        //  If not, return list of result.....
        //  @see https://www.doctrine-project.org/projects/doctrine-orm/en/2.14/reference/association-mapping.html
        throw new \LogicException('Reversed lookup not yet implemented');
    }

    protected function toRelationResult(Entity $entity, Column $column, Entity $rEntity): mixed
    {
        if ($rEntity->isNew()) {
            throw new \RuntimeException(sprintf('%s: %s is not yet persisted, 
                so reference is not yet accessible',$entity::class, $rEntity::class));
        }

        /** @var Relation $relation */
        $relation = $column->getRelation();

        // TODO: could be others which return results when more relations implemented
        if (!$relation instanceof FKInterface) {
            throw new NoRelationResultException();
        }
        $rMap = $this->mappingResolver->resolve($rEntity::class);
        $rPropName = null;

        foreach ($rMap->columns as $propName => $rColumn) {
            if ($rColumn->name === $relation->getTargetReferenceColumn()) {
                $rPropName = $propName;
                break;
            }
        }

        if ($rPropName === null) {
            throw new \LogicException(sprintf('%s: Cannot find reference `%s`',
                $entity::class, $relation->getTargetReferenceColumn()));
        }
        $rEntityProp = new \ReflectionProperty($rEntity::class, $rPropName);
        $rEntityProp->setAccessible(true);

        return $rEntityProp->getValue($rEntity);
    }
}
