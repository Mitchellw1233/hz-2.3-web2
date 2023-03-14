<?php

namespace Slimfony\HttpFoundation\Bag;


use Slimfony\HttpFoundation\Exception\BadRequestException;

/**
 * @template K of array-key
 * @template V
 */
abstract class AbstractBag
{
    /**
     * @var array<K, V>
     */
    protected array $data;

    /**
     * @param array<K, V> $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param K $key
     * @param V $default
     *
     * @return V
     */
    public function get($key, $default = null)
    {
        return \array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }

    /**
     * Returns the parameters.
     *
     * @param K|null $key The name of the parameter to return or null to get them all
     *
     * @return array<K, V>
     */
    public function all($key = null): array
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
     *
     * @return K[]
     */
    public function keys(): array
    {
        return \array_keys($this->data);
    }

    /**
     * Returns true if the parameter is defined.
     *
     * @param K $key
     */
    public function has($key): bool
    {
        return \array_key_exists($key, $this->data);
    }

    /**
     * @param K $key
     * @param V $value
     *
     * @return void
     */
    public function set($key, $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * @param array<K, V> $data
     */
    public function add(array $data = []): void
    {
        $this->data = \array_replace($this->data, $data);
    }

    /**
     * Removes a parameter.
     *
     * @param K $key
     */
    public function remove($key): void
    {
        unset($this->data[$key]);
    }

    /**
     * Replaces the current parameters by a new set.
     *
     * @param array<K, V> $data
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
