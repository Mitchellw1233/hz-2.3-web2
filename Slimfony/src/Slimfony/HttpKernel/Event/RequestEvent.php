<?php

namespace Slimfony\HttpKernel\Event;

use Slimfony\HttpFoundation\Response;

class RequestEvent extends KernelEvent
{
    private Response|null $response = null;

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
        $this->stopPropagation();
    }

    public function hasResponse(): bool
    {
        return null !== $this->response;
    }
}