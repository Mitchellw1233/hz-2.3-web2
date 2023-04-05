<?php

namespace Slimfony\ORM\Mapping;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column
{
    public function __construct(
        public string $name,
        public string $type,
        public bool $nullable=false,
        public bool $unsigned=false,
        public bool $autoIncrement=false,
    ) {
    }
}