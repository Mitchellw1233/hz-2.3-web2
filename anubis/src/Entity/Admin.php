<?php

namespace App\Entity;

use App\Entity\Interface\IdentifierInterface;
use App\Entity\Interface\UserInterface;
use App\Entity\Trait\IdentifierTrait;
use App\Entity\Trait\UserTrait;
use Slimfony\ORM\Entity as BaseEntity;
use Slimfony\ORM\Mapping\Column;
use Slimfony\ORM\Mapping\Entity;

#[Entity('admin')]
class Admin extends BaseEntity implements UserInterface, IdentifierInterface
{
    use UserTrait;
    use IdentifierTrait;

    #[Column(name: 'id', type: 'serial', primaryKey: true, autoIncrement: true)]
    private int $id;
    #[Column(name: 'name', type: 'varchar(255)')]
    private string $name;
    #[Column(name: 'email', type: 'varchar(255)')]
    private string $email;
    #[Column(name: 'password', type: 'varchar(255)')]
    private string $password;

    /**
     * @param string $name
     * @param string $email
     * @param string $password
     */
    public function __construct(string $name, string $email, string $password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
