<?php

namespace Slimfony\Config;

class ConfigLoader
{
    /**
     * @var array{
     *      route: array {
     *          path: string,
     *          controller: string,
     *          methods: array<string>,
     *      }
     * }
     */
    protected array $routes;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        if (!file_exists(__DIR__."./config/config.json")) {
            // TODO: correcte Exception ofzo?
            throw new \Exception("Could not find config file in root dir");
        }

        $file = readfile(__DIR__."./config/config.json");
        $json = json_decode($file);

        $this->routes = $json['routes'];
    }

    /**
     * @return array{
     *      route: array {
     *          path: string,
     *          controller: string,
     *          methods: array<string>,
     *      }
     * }
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}