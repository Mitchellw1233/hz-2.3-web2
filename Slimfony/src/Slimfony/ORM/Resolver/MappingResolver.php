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
     * @return array<int, array{
     *          entity: Entity,
     *          columns: array<string, Column>
     *      }>
     */
    public function resolve(): array {
        $mapping = [];

        for ($i = 0; $i < count($this->entities); $i++) {
            $entity = $this->entities[$i];
            try {
                $rEntity = new \ReflectionClass($entity);
            } catch (\ReflectionException) {
                throw new \LogicException('Entity "'.$entity.'" does not exist');
            }
            $mapping[$i]['entity'] = $rEntity->getAttributes()[0]->newInstance();

            foreach ($rEntity->getProperties() as $property) {
                $propertyAttribute = $property->getAttributes()[0];
                if ($propertyAttribute === null) {
                    continue;
                }

                $mapping[$i]['columns'][$property->getName()] = $propertyAttribute->newInstance();
            }
        }

        return $mapping;
    }
}