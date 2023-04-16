<?php

namespace App\Entity\Interface;

interface UserInterface
{
    /**
     * Gets id of user, whatever this may be
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Should be unique.
     *
     * @return string
     */
    public function getEmail(): string;

    /**
     * Gets name/full name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Gets hashed password
     *
     * @return string
     */
    public function getPassword(): string;

    /**
     * Hashes the password and sets it afterwards
     *
     * @param string $password
     *
     * @return void
     */
    public function setPassword(string $password): void;

    /**
     * Sets password without hashing it first
     *
     * @param string $password
     *
     * @return void
     */
    public function setPasswordRaw(string $password): void;

    /**
     * Checks if passwords match
     *
     * @param string $password
     *
     * @return bool
     */
    public function verifyPassword(string $password): bool;
}
