<?php

namespace Slimfony\DependencyInjection;

interface ContainerInterface
{
    public function has(string $key): bool;
    public function get(string $key): mixed;
}
