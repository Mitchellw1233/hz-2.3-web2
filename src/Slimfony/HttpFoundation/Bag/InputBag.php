<?php

namespace Slimfony\HttpFoundation\Bag;

use Slimfony\HttpFoundation\Exception\BadRequestException;

/**
 * @template K as string
 * @template V as string|int|float|bool|null
 *
 * @extends AbstractBag<K, V>
 */
final class InputBag extends AbstractBag
{
    /**
     * @param array<string, string|int|float|bool|null> $data
     *
     * @throws BadRequestException
     */
    public function __construct(array $data)
    {
        try {
            parent::__construct($data);
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestException($exception->getMessage());
        }
    }

    /**
     * @param string $key
     * @param string|int|float|bool|null $default
     *
     * @return string|int|float|bool|null
     */
    public function get($key, $default = null): string|int|float|bool|null
    {
        if (null !== $default && !\is_scalar($default) && !$default instanceof \Stringable) {
            throw new \InvalidArgumentException(sprintf('Expected a scalar value as a 2nd argument to "%s()", "%s" given.', __METHOD__, get_debug_type($default)));
        }

        $value = parent::get($key, $default);

        if (!\is_scalar($value) && !$value instanceof \Stringable) {
            throw new BadRequestException(sprintf('Input value "%s" contains a non-scalar value.', $key));
        }

        return $value;
    }

    // TODO: Until "@template" fixed
    /**
     * Sets an input by name.
     *
     * @param string $key
     * @param string|int|float|bool|array|null $value
     *
     * @throws \InvalidArgumentException
     */
    public function set($key, mixed $value): void
    {
        if (null !== $value && !\is_scalar($value) && !\is_array($value) && !$value instanceof \Stringable) {
            throw new \InvalidArgumentException(sprintf('Input value "%s" contains a non-scalar value.', $key));
        }

        parent::set($key, $value);
    }

    /**
     * @inheritDoc
     *
     * @param string|null $key
     *
     * @return array<string, string|int|float|bool|null>
     */
    public function all($key = null): array
    {
        return parent::all($key);
    }

    /**
     * @inheritDoc
     *
     * @return string[]
     */
    public function keys(): array
    {
        return parent::keys();
    }

    /**
     * @inheritDoc
     *
     * @param string $key
     */
    public function has($key): bool
    {
        return parent::has($key);
    }

    /**
     * @inheritDoc
     *
     * @param array<string, string|int|float|bool|null> $data
     */
    public function add(array $data = []): void
    {
        parent::add($data);
    }

    /**
     * @inheritDoc
     *
     * @param string $key
     */
    public function remove($key): void
    {
        parent::remove($key);
    }

    /**
     * @inheritDoc
     *
     * @param array<string, string|int|float|bool|null> $data
     */
    public function replace(array $data = []): void
    {
        parent::replace($data);
    }

    /**
     * @inheritDoc
     *
     * @param string $key
     * @param string|int|float|bool|null $value
     */
    public function contains($key, $value): bool
    {
        return parent::contains($key, $value);
    }


}
