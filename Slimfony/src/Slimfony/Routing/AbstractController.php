<?php

namespace Slimfony\Routing;

use Slimfony\HttpFoundation\RedirectResponse;
use Slimfony\HttpFoundation\Request;
use Slimfony\HttpFoundation\Response;
use Slimfony\HttpKernel\Kernel;
use Slimfony\Templating\Template;
use Slimfony\DependencyInjection\Container;

abstract class AbstractController
{
    private Template $template;
    private ?Request $request = null;

    /**
     * @param Container $container
     */
    public function __construct(
        protected Container $container,
        protected RouteResolver $routeResolver,
    ) {
        $this->template = $this->container->get(Template::class);
    }

    /**
     * @param string $viewPath
     * @param array<string, mixed> $parameters
     * @return Response
     */
    public function render(string $viewPath, array $parameters = []): Response
    {
        return new Response($this->template->render($viewPath, $parameters, [
            'request' => $this->getRequest(),
            'user' => $this->getUser(),
        ]));
    }

    public function redirect(string $location): RedirectResponse
    {
        return new RedirectResponse($location);
    }

    public function redirectToRoute(string $routeName, array $parameters = [], array $query = []): RedirectResponse
    {
        $route = $this->routeResolver->resolveRouteByName($routeName);
        if ($route === null) {
            throw new \InvalidArgumentException(sprintf('Route name %s cannot be found', $routeName));
        }
        $route->fillParameters($parameters);

        $queryStr = empty($query) ? '' : '?' . http_build_query($query);

        return new RedirectResponse(
            $this->getRequest()->getUri()->getBase()
            . $route->buildPath()
            . $queryStr
        );
    }

    public function getRequest(): Request
    {
        if ($this->request === null) {
            $this->request = Kernel::$request ?? throw new \LogicException('Kernel did not set a request');
        }

        return $this->request;
    }
}
