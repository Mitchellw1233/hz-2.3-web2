<?php

namespace Slimfony\Routing;

class Route
{
    protected string $name;
    protected string $path;
    protected string $controllerPath;
    /**
     * @var array<int, string>
     */
    protected array $methods;
    protected array $parameters;

    /**
     * @param string $name
     * @param string $path
     * @param string $controllerPath the absolute path of the controller
     * @param array<int, string> $methods
     */
    public function __construct(string $name, string $path, string $controllerPath, array $methods)
    {
        $this->setName($name);
        $this->setPath($path);
        $this->setControllerPath($controllerPath);
        $this->setMethods($methods);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getControllerPath(): string
    {
        return $this->controllerPath;
    }

    /**
     * @return array<int, string>
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @return array<string, string>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
        $this->setParameters();
    }

    /**
     * @param string $controllerPath
     */
    public function setControllerPath(string $controllerPath): void
    {
        $this->controllerPath = $controllerPath;
    }

    /**
     * @param array<int, string> $methods
     */
    public function setMethods(array $methods): void
    {
        $this->methods = $methods;
    }

    protected function setParameters(): void
    {
        $this->parameters = [];
        // Get all parameters brackets {}
        preg_match_all('#\{[^\}]++\}#', $this->path, $parameters);

        foreach ($parameters[0] as $p) {
            // Retrieve key => value
            preg_match('#\{([\w\x80-\xFF]++)?(\?[^\}]*+)?\}#', $p, $m);
            $this->parameters[$m[1]] = isset($m[2]) ? substr($m[2], 1) : null;
        }
    }
}
