<?php

namespace App\Entity\Trait;

use Slimfony\ORM\Mapping\Column;

trait IdentifierTrait
{
    #[Column(name: 'id', type: 'serial', autoIncrement: true)]
    private int $id;

    public function getId(): int
    {
        if (!isset($this->id)) {
            throw new \LogicException('Called id while not initialized yet');
        }

        return $this->id;
    }
}
