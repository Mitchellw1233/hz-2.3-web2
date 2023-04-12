<?php

namespace Slimfony\Templating;

class Template
{
    private string $path;
    private array $blocks = [];
    private ?string $currentBlock = null;

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

        if (array_key_exists('global', $parameters)) {
            throw new \InvalidArgumentException('global is a reserved keyword');
        }

        // Global
        $parameters['global'] = $this->getGlobalVars();

        // Blocks
        $self = $this;
        $parameters['start_block'] = static function (string $blockName) use ($self) {
            $self->currentBlock = $blockName;
            ob_start();
        };
        $parameters['end_block'] = static function () use ($self) {
            if ($self->currentBlock === null) {
                throw new \LogicException('No block to be ended');
            }
            $self->blocks[$self->currentBlock] = ob_get_clean();
        };
        $parameters['get_block'] = static function (string $blockName) use ($self) {
            return $self->blocks[$blockName] ?? '';
        };

        // Initiate template
        extract($parameters);
        ob_start();
        include ($file);

        return ob_get_clean();
    }

    private function getGlobalVars(): array
    {
        return [
            'basePath' => $this->path,
            // TODO: Globals
        ];
    }
}
