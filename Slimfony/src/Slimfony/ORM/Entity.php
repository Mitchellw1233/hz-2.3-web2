<?php

namespace Slimfony\ORM;

class Entity
{
    private bool $isNew = false;

    public function isNew(): bool
    {
        return $this->isNew;
    }
}
