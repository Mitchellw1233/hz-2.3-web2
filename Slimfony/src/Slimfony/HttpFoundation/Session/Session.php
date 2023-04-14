<?php

namespace Slimfony\HttpFoundation\Session;

class Session implements SessionInterface
{
    protected ?string $sessionId;

    public function __construct()
    {
        if (\PHP_SESSION_ACTIVE !== session_status()) {
            if (!session_start()) {
                throw new \RuntimeException('Failed to start the session');
            }
        }

        $this->sessionId = $_COOKIE[session_name()] ?? null;
    }

    public function get(string $key, $default=null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function getId(): string
    {
        return session_id();
    }

    public function getName(): string
    {
        return session_name();
    }

    public function all(): array
    {
        return $_SESSION;
    }

    public function replace(array $attributes): void
    {
        $_SESSION = array_replace($_SESSION, $attributes);
    }

    public function remove(string $key): mixed
    {
        if (!$this->has($key)) {
            return null;
        }

        $value = $_SESSION[$key];
        unset($_SESSION[$key]);

        return $value;
    }


    public function clear(): void
    {
        session_unset();
    }

    public function regenerateId(): void
    {
        session_regenerate_id();
    }
}
