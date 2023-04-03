<?php

namespace Slimfony\DependencyInjection;

class Reference
{
    public function __construct(
        public string $fqn,
    )
    {
    }
}