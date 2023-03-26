<?php

namespace Slimfony\HttpFoundation\Bag;

class ParameterBag extends AbstractBag
{
    // TODO: Until "@template" fixed
    /**
     * @inheritDoc
     *
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
    }

    /**
     * @inheritDoc
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return parent::get($key, $default);
    }

    /**
     * @inheritDoc
     *
     * @param string|null $key
     *
     * @return array<string, mixed>
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
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value): void
    {
        parent::set($key, $value);
    }

    /**
     * @inheritDoc
     *
     * @param array<string, mixed> $data
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
     * @param array<string, mixed> $data
     */
    public function replace(array $data = []): void
    {
        parent::replace($data);
    }

    /**
     * @inheritDoc
     *
     * @param string $key
     * @param mixed $value
     */
    public function contains($key, $value): bool
    {
        return parent::contains($key, $value);
    }
}
