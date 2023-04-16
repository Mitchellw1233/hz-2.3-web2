<?php

namespace Slimfony\DependencyInjection;

use Slimfony\DependencyInjection\Exception\ServiceNotFoundException;

class Container implements ContainerInterface
{
    /**
     * @var array<string, \Closure>
     */
    protected array $services;
    /**
     * @var array<string, mixed>
     */
    protected array $sharedServices;

    public function __construct()
    {
        $this->services = [];
        $this->sharedServices = [];
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->services);
    }

    /**
     * @template T
     *
     * @throws ServiceNotFoundException
     *
     * @psalm-param class-string<T> $key
     * @psalm-return T
     */
    public function get(string $key)
    {
        if (array_key_exists($key, $this->sharedServices)) {
            return $this->sharedServices[$key];
        }

        return $this->services[$key]() ?? throw new ServiceNotFoundException($key);
    }

    /**
     * @param string $key
     * @param \Closure $factory
     * @param bool
     *
     * @return void
     */
    public function set(string $key, \Closure $factory, bool $shared = true): void
    {
        if ($shared) {
            $this->sharedServices[$key] = $factory();
        }

        $this->services[$key] = $factory;
    }
}
