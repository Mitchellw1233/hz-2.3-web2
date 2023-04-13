<?php

namespace Slimfony\ORM;

class Entity
{
    private bool $isNew = true;

    public function isNew(): bool
    {
        return $this->isNew;
    }
}
