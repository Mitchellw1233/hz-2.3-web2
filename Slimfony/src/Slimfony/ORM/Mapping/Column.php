<?php

namespace Slimfony\ORM\Mapping;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column
{

    protected Relation $relation;

    public function __construct(
        public string $name,
        public string $type,
        public bool $primaryKey=false,
        public bool $nullable=false,
        public bool $unique=false,
        public bool $unsigned=false,
        public bool $autoIncrement=false,
    ) {
    }

    /**
     * @param Relation $relation
     * @return void
     */
    public function setRelation(Relation $relation)
    {
        $this->relation = $relation;
    }

    /**
     * @return bool
     */
    public function hasRelation(): bool
    {
        // CHECK: kan dit ook is_null zijn? Ik krijg hier namelijk iets onder dat het niet geinitieerd is wat je vast kut vind haha
        return $this->relation !== null;
    }
}