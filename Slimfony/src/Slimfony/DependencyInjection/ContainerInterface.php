<?php

namespace Slimfony\DependencyInjection;

use Slimfony\DependencyInjection\Exception\ServiceNotFoundException;

interface ContainerInterface
{
    /**
     * @param class-string $key
     *
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * @template T
     *
     * @throws ServiceNotFoundException
     *
     * @psalm-param class-string<T> $key
     * @psalm-return T
     */
    public function get(string $key);
}
