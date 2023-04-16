<?php

namespace Slimfony\Validation;

class Constraint
{
    public function __construct(
        public string $type,
        public bool $nullable = false,
        public bool $empty = false,
        public bool $onEmptyReturn = false  // TODO: Temp
    ) {
    }
}