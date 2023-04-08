<?php

namespace App\Entity;

use Slimfony\ORM\Entity as BaseEntity;
use Slimfony\ORM\Mapping\Column;
use Slimfony\ORM\Mapping\Entity;

#[Entity('user')]
class User extends BaseEntity
{
    #[Column('id', 'int', true, unsigned: true, autoIncrement: true)]
    private int $id;

    #[Column('username', 'varchar')]
    private string $username;
}
