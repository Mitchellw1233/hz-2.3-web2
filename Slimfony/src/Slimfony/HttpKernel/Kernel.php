<?php

namespace Slimfony\HttpKernel;

use Slimfony\EventDispatcher\EventDispatcher;
use Slimfony\HttpFoundation\Request;
use Slimfony\HttpFoundation\Response;
use Slimfony\HttpKernel\Event\RequestEvent;
use Slimfony\HttpKernel\Event\ViewEvent;
use Slimfony\HttpKernel\Exception\HttpException;
use Slimfony\HttpKernel\Exception\NotFoundHttpException;
use Slimfony\Routing\Route;

abstract class Kernel implements KernelInterface
{
    public function __construct(
        protected EventDispatcher $dispatcher,
        protected ControllerResolver $controllerResolver,
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $response = $this->handleRaw($request);
        } catch (HttpException $e) {
            $response = new Response($e->getMessage());
            $response->setStatusCode($e->getStatusCode());
        }

        return $response;
    }

    public function handleRaw(Request $request): Response
    {
        $this->dispatcher->dispatch(new RequestEvent($this, $request));

        $route = $request->getAttributes()->get('_route');
        if (!$route instanceof Route) {
            throw new NotFoundHttpException();
        }

        $controller = $this->controllerResolver->getController($route);
        $result = $controller(...$this->controllerResolver->getArguments($route, $controller));

        $viewEvent = new ViewEvent($this, $request, $result);
        $this->dispatcher->dispatch($viewEvent);

        $response = $viewEvent->getResponse() ?? $result;
        if (false === $response instanceof Response) {
            throw new \LogicException(sprintf('Expected Response type, `%s` given', gettype($response)));
        }

        return $response;
    }
}
