<?php

namespace App\Entity;

use Slimfony\ORM\Mapping\Entity;
use Slimfony\ORM\Mapping\Column;

#[Entity('post')]
class Post
{
    #[Column('posted_by', 'int', false, true)]
    public int $postedBy;
    #[Column('title', 'varchar')]
    public string $title;
}