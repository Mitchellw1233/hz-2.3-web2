<?php

namespace App\Entity;

use App\Entity\Interface\IdentifierInterface;
use App\Entity\Interface\UserInterface;
use App\Entity\Trait\IdentifierTrait;
use App\Entity\Trait\UserTrait;
use Slimfony\ORM\Entity as BaseEntity;
use Slimfony\ORM\Mapping\Column;
use Slimfony\ORM\Mapping\Entity;

#[Entity('teacher')]
class Teacher extends BaseEntity implements UserInterface, IdentifierInterface
{
    use UserTrait;
    use IdentifierTrait;

    #[Column(name: 'id', type: 'serial', primaryKey: true, autoIncrement: true)]
    private int $id;
    #[Column(name: 'first_name', type: 'varchar(255)')]
    private string $firstName;
    #[Column(name: 'last_name', type: 'varchar(255)')]
    private string $lastName;
    #[Column(name: 'email', type: 'varchar(255)')]
    private string $email;
    #[Column(name: 'password', type: 'varchar(255)')]
    private string $password;
    #[Column(name: 'birth_date', type: 'date')]
    private \DateTime $birthDate;

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param \DateTime $birthDate
     */
    public function __construct(string $firstName, string $lastName, string $email, \DateTime $birthDate)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->birthDate = $birthDate;

        // TODO: REMOVE
        $this->password = "testpassword";
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getName(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
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

    /**
     * @return \DateTime
     */
    public function getBirthDate(): \DateTime
    {
        return $this->birthDate;
    }

    /**
     * @param \DateTime $birthDate
     */
    public function setBirthDate(\DateTime $birthDate): void
    {
        $this->birthDate = $birthDate;
    }
}
