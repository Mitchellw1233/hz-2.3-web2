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
}