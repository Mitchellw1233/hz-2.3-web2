<?php

namespace Slimfony\ORM;

use Slimfony\ORM\Query\EntityQueryBuilder;

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
            $isNewProp = new \ReflectionProperty($className, 'isNew');
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
                $prop->setValue($this->fromRelation($column, $dbResult));
                continue;
            }

            // If column name not in dbResult
            if (!array_key_exists($column->name, $dbResult)) {
                throw new \LogicException(sprintf('column `%s` is not in result set', $column->name));
            }

            $prop->setValue($class, $dbResult[$column->name]);
        }

        return $class;

        // Map parameter names to values from associative array
//        $args = [];
//        foreach ($rc->getConstructor()?->getParameters() as $param) {
//            $name = $param->getName();
//            if (!array_key_exists($name, $arr) && !$param->isOptional()) {
//                throw new \InvalidArgumentException("Missing required parameter $name");
//            }
//
//            $args[] = $arr[$name];
//        }
//
//        try {
//            $class = new $className(...$args);
//            $isNewProp = new \ReflectionProperty($class::class, 'isNew');
//        } catch (\Exception $e) {
//            throw new \LogicException($e->getMessage());
//        }
//
//        $isNewProp->setAccessible(true);
//        $isNewProp->setValue($class, $isNew);
//
//        return $class;
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
            try {
                $prop = new \ReflectionProperty($entity::class, $propName);
            } catch (\ReflectionException $e) {
                throw new \LogicException($e->getMessage());
            }
            $prop->setAccessible(true);

            $result[$column->name] = $prop->getValue($entity);
        }

        return $result;
    }

    /**
     * @param Column $column
     * @param array<string, mixed> $dbResult
     * @return mixed
     */
    protected function fromRelation(Column $column, array $dbResult): mixed
    {
        if (!array_key_exists($column->name, $dbResult)) {
            throw new \LogicException(sprintf('column `%s` is not in result set', $column->name));
        }

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
}
