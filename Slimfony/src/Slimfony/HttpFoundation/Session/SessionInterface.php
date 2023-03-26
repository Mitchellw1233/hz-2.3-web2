<?php

namespace Slimfony\HttpFoundation\Session;

/**
 * Interface for the session.
 */
interface SessionInterface
{
    /**
     * Returns an attribute.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Sets an attribute.
     */
    public function set(string $key, mixed $value): void;

    /**
     * Checks if an attribute is defined.
     */
    public function has(string $key): bool;

    /**
     * Returns the session ID.
     */
    public function getId(): string;

    /**
     * Returns the session name.
     */
    public function getName(): string;

    /**
     * Returns attributes.
     *
     * @return array<string, mixed>
     */
    public function all(): array;

    /**
     * Sets attributes.
     *
     * @param array<string, mixed> $attributes
     *
     * @return void
     */
    public function replace(array $attributes): void;

    /**
     * Removes an attribute.
     *
     * @return mixed The removed value or null when it does not exist
     */
    public function remove(string $key): mixed;

    /**
     * Clears all attributes.
     *
     * @return void
     */
    public function clear(): void;

    /**
     * Regenerates session id.
     */
    public function regenerateId(): void;
}
