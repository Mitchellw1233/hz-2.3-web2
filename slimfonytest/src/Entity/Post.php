<?php

namespace App\Entity;

use Slimfony\ORM\Mapping\Entity;
use Slimfony\ORM\Mapping\Column;
use Slimfony\ORM\Mapping\FKRelation;

#[Entity('post')]
class Post
{
    #[Column('id', 'int', true, unsigned: true, autoIncrement: true)]
    public int $id;

    #[FKRelation(User::class, 'id')]
    #[Column('posted_by', 'int', unsigned: true)]
    public int $postedBy;

    #[Column('title', 'varchar')]
    public string $title;
}