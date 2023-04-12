<?php

namespace App\Entity\Trait;

trait IdentifierTrait
{
    private int $id;

    public function getId(): int
    {
        if (!isset($this->id)) {
            throw new \LogicException('Called id while not initialized yet');
        }

        return $this->id;
    }
}
