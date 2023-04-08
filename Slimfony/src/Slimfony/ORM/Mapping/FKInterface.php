<?php

namespace Slimfony\ORM\Mapping;

interface FKInterface
{
    public function getTargetReferenceColumn(): string;
}