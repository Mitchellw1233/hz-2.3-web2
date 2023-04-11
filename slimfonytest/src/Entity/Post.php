<?php

namespace App\Entity;

use Slimfony\ORM\Entity as BaseEntity;
use Slimfony\ORM\Mapping\Entity;
use Slimfony\ORM\Mapping\Column;
use Slimfony\ORM\Mapping\FKRelation;

#[Entity('post')]
class Post extends BaseEntity
{
    #[Column('id', 'serial', primaryKey: true, unsigned: true, autoIncrement: true)]
    private int $id;

    #[FKRelation(User::class, 'id')]
    #[Column('posted_by', 'integer', unsigned: true)]
    private User $postedBy;

    #[Column('title', 'varchar')]
    private string $title;
}
