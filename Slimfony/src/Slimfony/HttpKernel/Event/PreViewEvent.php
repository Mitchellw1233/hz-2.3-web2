<?php

namespace Slimfony\HttpKernel\Event;

use Slimfony\HttpFoundation\Response;
use Slimfony\Routing\Route;

class PreViewEvent extends KernelEvent
{
    private Response|null $response = null;

    public function getRoute(): Route
    {
        $route = $this->getRequest()->getAttributes()->get('_route');
        if (!$route instanceof Route) {
            throw new \InvalidArgumentException('Route has not been set into request');
        }
        return $route;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
        $this->stopPropagation();
    }

    public function hasResponse(): bool
    {
        return null !== $this->response;
    }
}