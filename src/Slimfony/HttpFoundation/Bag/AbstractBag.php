<?php

namespace Slimfony\HttpFoundation\Bag;


use Slimfony\HttpFoundation\Exception\BadRequestException;

/**
 * @template T
 */
abstract class AbstractBag
{
    /**
     * @var T[]
     */
    protected array $data;

    /**
     * @param T[] $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $key
     * @param T $default
     *
     * @return T
     */
    public function get(string $key, $default = null): mixed
    {
        return \array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }

    /**
     * Returns the parameters.
     *
     * @param string|null $key The name of the parameter to return or null to get them all
     */
    public function all(string $key = null): array
    {
        if (null === $key) {
            return $this->data;
        }

        if (!\is_array($value = $this->data[$key] ?? [])) {
            throw new BadRequestException(sprintf('Unexpected value for parameter "%s": expecting "array", got "%s".', $key, get_debug_type($value)));
        }

        return $value;
    }

    /**
     * Returns the parameter keys.
     */
    public function keys(): array
    {
        return \array_keys($this->data);
    }

    /**
     * Returns true if the parameter is defined.
     */
    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->data);
    }

    /**
     * @param string $key
     * @param T $value
     *
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * @param T[] $data
     */
    public function add(array $data = []): void
    {
        $this->data = \array_replace($this->data, $data);
    }

    /**
     * Removes a parameter.
     */
    public function remove(string $key): void
    {
        unset($this->data[$key]);
    }

    /**
     * Replaces the current parameters by a new set.
     */
    public function replace(array $data = []): void
    {
        $this->data = $data;
    }

    /**
     * Returns the number of parameters.
     */
    public function count(): int
    {
        return \count($this->data);
    }
}
