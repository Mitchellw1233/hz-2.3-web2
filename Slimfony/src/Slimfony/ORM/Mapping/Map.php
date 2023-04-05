<?php

namespace Slimfony\ORM\Mapping;

class Map
{
    /**
     * @param Entity $entity
     * @param array<string, Column> $columns
     */
    public function __construct(
        public Entity $entity,
        public array $columns,
    ) {
    }
}