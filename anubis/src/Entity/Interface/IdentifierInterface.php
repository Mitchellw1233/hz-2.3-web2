<?php

namespace App\Entity\Interface;

interface IdentifierInterface
{
    /**
     * @throws \LogicException when entity is not persisted yet
     *
     * @return int
     */
    public function getId(): int;
}
