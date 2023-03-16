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
        $this->data = [];

        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param K $key
     * @param V $default
     *
     * @return V|null
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
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
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
        $this->data = [];
        $this->add($this->data);
    }

    /**
     * Returns true if data contains array
     *
     * @param K $key
     * @param V $value
     *
     * @return bool
     */
    public function contains($key, $value): bool
    {
        return \in_array($value, $this->all($key));
    }

    /**
     * Filter key.
     *
     * @param K $key
     * @param int $filter FILTER_* constant
     *
     * @see https://php.net/filter-var
     */
    public function filter($key, mixed $default = null, int $filter = \FILTER_DEFAULT, mixed $options = []): mixed
    {
        $value = $this->get($key, $default);

        // Always turn $options into an array - this allows filter_var option shortcuts.
        if (!\is_array($options) && $options) {
            $options = ['flags' => $options];
        }

        // Add a convenience check for arrays.
        if (\is_array($value) && !isset($options['flags'])) {
            $options['flags'] = \FILTER_REQUIRE_ARRAY;
        }

        if ((\FILTER_CALLBACK & $filter) && !(($options['options'] ?? null) instanceof \Closure)) {
            throw new \InvalidArgumentException(sprintf('A Closure must be passed to "%s()" when FILTER_CALLBACK is used, "%s" given.', __METHOD__, get_debug_type($options['options'] ?? null)));
        }

        return filter_var($value, $filter, $options);
    }

    /**
     * Returns the number of parameters.
     */
    public function count(): int
    {
        return \count($this->data);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->data);
    }
}
