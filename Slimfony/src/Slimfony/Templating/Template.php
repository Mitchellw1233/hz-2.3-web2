<?php

namespace Slimfony\Templating;

class Template
{
    private string $path;

    /**
     * Template constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = rtrim($path, '/').'/';
    }

    /**
     * @param string $viewPath
     * @param array<string, mixed> $parameters
     * @return string
     * @throws \LogicException
     */
    public function render(string $viewPath, array $parameters = []): string
    {
        if (!file_exists($file = $this->path.$viewPath)) {
            throw new \LogicException(sprintf('The file %s could not be found.', $viewPath));
        }

        extract($parameters);

        ob_start();

        include ($file);

        return ob_get_clean();
    }
}