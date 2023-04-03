<?php

namespace Slimfony\Routing;

use Slimfony\HttpFoundation\Response;
use Slimfony\Templating\Template;
use Slimfony\DependencyInjection\Container;

abstract class AbstractController
{
    /**
     * @param Container $container
     */
    public function __construct(
        protected Container $container,
    ) {
    }

    /**
     * @param string $viewPath
     * @param array<string, mixed> $parameters
     * @return Response
     */
    public function render(string $viewPath, array $parameters): Response
    {
        $template = $this->container->get(Template::class);
        $content = $template->render($viewPath, $parameters);
        return new Response($content);
    }
}
