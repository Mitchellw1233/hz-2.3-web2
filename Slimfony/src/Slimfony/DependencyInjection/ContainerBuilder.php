<?php

namespace Slimfony\DependencyInjection;

class ContainerBuilder
{
    protected Container $container;

    public function __construct()
    {
        $this->container = new Container();
        $this->container->set(Container::class, fn () => $this->container, true);
    }

    /**
     * @param string $class as FQN
     * @param array<int, mixed|Reference> $args if filled, auto-wiring is disabled
     * @param array<int, array{method: string, args?: array<int, mixed|Reference>}> $methodCalls methods to call on class, before returning
     * @param bool $shared
     *
     * @return void
     */
    public function register(string $class, array $args = [], array $methodCalls = [], bool $shared = true): void
    {
        // Check if class exists
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('class `%s` does not exist', $class));
        }

        // If args not empty: Filter out Reference and get container
        for ($i = 0; $i < count($args); $i++) {
            if (!$args[$i] instanceof Reference) {
                continue;
            }
            $fqn = $args[$i]->fqn;
            if (!$this->container->has($fqn)) {
                throw new \InvalidArgumentException(sprintf('`%s` not found as service building `%s`',
                    $fqn, $class));
            }
            $args[$i] = $this->container->get($fqn);
        }

        // If methodCalls not empty: Replace all Reference objects by container factory
        for ($i = 0; $i < count($methodCalls); $i++) {
            for ($y = 0; $y < count($methodCalls[$i]['args'] ?? []); $y++) {
                if (!$methodCalls[$i]['args'][$y] instanceof Reference) {
                    continue;
                }
                $fqn = $methodCalls[$i]['args'][$y]->fqn;
                if (!$this->container->has($fqn)) {
                    throw new \InvalidArgumentException(sprintf('`%s` not found as service building `%s`',
                        $fqn, $class));
                }
                $methodCalls[$i]['args'][$y] = $this->container->get($fqn);
            }
        }

        // if args not empty: auto-wiring is false, so construct args are manually filled
        // if method does not exist, auto-wiring is true, but no wiring is necessary
        if (!empty($args) || !method_exists($class, '__construct')) {
            $this->registerContainerFactory($class, $args, $methodCalls, $shared);
            return;
        }

        // auto-wiring is true, so construct args will be extracted from given class and automatically filled
        try {
            $rc = new \ReflectionMethod($class, '__construct');
        } catch (\ReflectionException $e) {
            throw new \LogicException($e->getMessage());
        }

        // Loop through construct parameters and try to auto-wire the type of it
        foreach ($rc->getParameters() as $param) {
            $paramClass = $param->getType()->getName();

            // If not a class or does not exist in container
            if (!class_exists($paramClass) || !$this->container->has($paramClass)) {
                throw new \LogicException(sprintf('param `%s` of type `%s` cannot be wired automatically 
                for class `%s`.', $param->getName(), $paramClass, $class
                ));
            }

            $args[] = $this->container->get($paramClass);
        }

        $this->registerContainerFactory($class, $args, $methodCalls, $shared);
    }

    /**
     * @param $services array<string, array{
     *     args?: array<int, mixed|Reference>,
     *     methods?: array<int, array{method: string, args?: array<int, mixed|Reference>}>,
     *     shared?: bool
     * }>
     */
    public function registerAll(array $services): void
    {
        // TODO: Order by referenced, so we don't have to order ourselves
        foreach ($services as $class => $config) {
            $this->register($class, $config['args'] ?? [], $config['methods'] ?? [], $config['shared'] ?? true);
        }
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @param string $class as FQN
     * @param array<int, mixed|Reference> $args if filled, auto-wiring is disabled
     * @param array<int, array{method: string, args?: array<int, mixed|Reference>}> $methodCalls methods to call on class, before returning
     * @param bool $shared
     *
     * @return void
     */
    private function registerContainerFactory(string $class, array $args, array $methodCalls, bool $shared = true): void
    {
        // TODO: Check if this does not generate error, else:
        //  InvalidArgumentException: new class not FQN or args not correct or methodCalls (method or mArgs) not correct
        $this->container->set($class, function () use ($class, $args, $methodCalls) {
            $instance = new $class(...$args);

            foreach ($methodCalls as $methodCall) {
                $instance->{$methodCall['method']}(...$methodCall['args'] ?? []);
            }

            return $instance;
        }, $shared);
    }
}
