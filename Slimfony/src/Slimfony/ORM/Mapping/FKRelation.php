<?php

namespace Slimfony\ORM\Mapping;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class FKRelation extends Relation implements FKInterface
{
    protected string $targetReferenceColumn;

    /**
     * @param string $targetEntity as FQN
     * @param string $targetReferenceColumn
     */
    public function __construct(
        string $targetEntity,
        string $targetReferenceColumn,
    ) {
        parent::__construct($targetEntity);
        $this->targetReferenceColumn = $targetReferenceColumn;
    }

    /**
     * @return string
     */
    public function getTargetReferenceColumn(): string
    {
        return $this->targetReferenceColumn;
    }
}