<?php

namespace Slimfony\Config;

class ConfigLoader
{
    /**
     * @var array<string, array{
     *     path: string,
     *     controller: string,
     *     methods: array<string>|null,
     * }>
     */
    protected array $routes;

    /**
     * @var array{
     *      database: string,
     *      username: string,
     *      password: string,
     *      host: string,
     *      port: int,
     * }
     */
    protected array $db;

    /**
     * @throws \LogicException
     */
    public function __construct(string $projectDir)
    {
        // TODO: Dependency injection of kernel with getProjectDir() or argument injection
        if (!file_exists($projectDir.'/config/config.json')) {
            throw new \LogicException('Could not find config file in root dir');
        }

        $file = file_get_contents($projectDir.'/config/config.json');
        try {
            $json = json_decode($file, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new \LogicException('Could not correctly parse config.json from json');
        }

        $this->setRoutes($json['routes']);
        $this->setDb($json['db']);
    }

    /**
     * @return array<string, array{
     *     path: string,
     *     controller: string,
     *     methods: array<string>|null,
     * }>
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @param array<string, array{
     *     path: string,
     *     controller: string,
     *     methods: array<string>|null,
     * }> $routes
     */
    protected function setRoutes(array $routes): void
    {
        $data = [];
        foreach ($routes as $name => $route) {
            // Check if methods is set, else empty array
            if (!isset($route['methods'])) {
                $route['methods'] = null;
            }
            $data[$name] = $route;
        }

        $this->routes = $data;
    }

    /**
     * @return array{
     *      database: string,
     *      username: string,
     *      password: string,
     *      host: string,
     *      port: int,
     * }
     */
    public function getDb(): array
    {
        return $this->db;
    }

    /**
     * @param array{
     *      database: string,
     *      username: string,
     *      password: string,
     *      host: string,
     *      port: int,
     * } $db
     */
    protected function setDb(array $db): void
    {
        $this->db = $db;
    }
}
