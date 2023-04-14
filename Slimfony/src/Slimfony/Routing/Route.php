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
    protected array|null $methods;

    /**
     * @var array<string, string>
     */
    protected array $parameters;

    /**
     * @param string $name
     * @param string $path
     * @param string $controllerPath the absolute path of the controller
     * @param array<int, string>|null $methods
     */
    public function __construct(string $name, string $path, string $controllerPath, array|null $methods)
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

    public function buildPath(): string
    {
        $parameters = array_values($this->parameters);
        $i = -1;
        return preg_replace_callback('#\{[^\}]++\}#', function () use ($parameters, $i) {
            $i++;
            return $parameters[$i];
        }, $this->path);
    }

    /**
     * @return string
     */
    public function getControllerPath(): string
    {
        return $this->controllerPath;
    }

    /**
     * @return array<int, string>|null
     */
    public function getMethods(): array|null
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
     * @param array<int, string>|null $methods
     */
    public function setMethods(array|null $methods): void
    {
        $this->methods = $methods;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return void
     */
    public function fillParameter(string $key, string $value): void
    {
        if (!array_key_exists($key, $this->getParameters())) {
            throw new \LogicException('Array key does not exist in parameters');
        }

        $this->parameters[$key] = $value;
    }

    /**
     * @param array<string, string> $parameters
     *
     * @return void
     */
    public function fillParameters(array $parameters): void
    {
        foreach ($parameters as $key => $value) {
            $this->fillParameter($key, $value);
        }
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
