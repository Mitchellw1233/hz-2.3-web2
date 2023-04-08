<?php

namespace App\Entity;

use Slimfony\ORM\Mapping\Column;
use Slimfony\ORM\Mapping\Entity;

#[Entity('user')]
class User
{
    #[Column('id', 'int', true, unsigned: true, autoIncrement: true)]
    public int $id;

    #[Column('username', 'varchar')]
    public string $username;
}