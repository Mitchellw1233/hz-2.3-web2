<?php

namespace Slimfony\ORM\Mapping;

abstract class Relation
{
    /**
     * @param string $targetEntity as FQN
     */
    public function __construct(
        protected string $targetEntity,
    ) {
    }

    /**
     * @return class-string
     */
    public function getTargetEntity(): string
    {
        return $this->targetEntity;
    }
}