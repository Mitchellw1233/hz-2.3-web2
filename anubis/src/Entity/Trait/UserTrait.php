<?php

namespace App\Entity\Trait;

/**
 * @property string $password
 */
trait UserTrait
{
    private string $password;

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    public function setPasswordRaw(string $password): void
    {
        $this->password = $password;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}
