<?php

namespace Slimfony\ORM\Mapping;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column
{
    protected ?Relation $relation = null;

    public function __construct(
        public string $name,
        public string $type,
        public bool $primaryKey=false,
        public bool $nullable=false,
        public bool $unique=false,
        public bool $unsigned=false,  // Not supported by PGSQL
        public bool $autoIncrement=false,
    ) {
    }

    /**
     * @param Relation $relation
     * @return void
     */
    public function setRelation(Relation $relation): void
    {
        $this->relation = $relation;
    }

    /**
     * @return bool
     */
    public function hasRelation(): bool
    {
        return $this->relation !== null;
    }

    public function getRelation(): ?Relation
    {
        return $this->relation;
    }
}