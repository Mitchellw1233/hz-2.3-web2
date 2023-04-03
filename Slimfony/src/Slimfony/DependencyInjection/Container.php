<?php

namespace Slimfony\DependencyInjection;

use Slimfony\DependencyInjection\Exception\ServiceNotFoundException;

class Container implements ContainerInterface
{
    /**
     * @var array<string, \Closure>
     */
    protected array $services;

    public function __construct()
    {
        $this->services = [];
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->services);
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function get(string $key): mixed
    {
        return $this->services[$key]() ?? throw new ServiceNotFoundException($key);
    }

    /**
     * @param string $key
     * @param \Closure $factory
     *
     * @return void
     */
    public function set(string $key, \Closure $factory): void
    {
        $this->services[$key] = $factory;
    }
}
